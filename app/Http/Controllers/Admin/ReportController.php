<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Partner;
use App\Models\Expense;
use App\Services\FinancialCalculationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $financialService;

    public function __construct(FinancialCalculationService $financialService)
    {
        $this->financialService = $financialService;
    }

    public function index()
    {
        // Calculate stats for reports overview
        $stats = [
            'total_expenses' => \App\Models\Expense::where('status', 'approved')->sum('amount'),
            'total_investments' => \App\Models\Investment::sum('amount'),
            'total_partner_payouts' => \App\Models\PartnerPayment::where('status', 'completed')->sum('amount'),
            'net_total' => 0, // Will be calculated
        ];

        $stats['net_total'] = $stats['total_investments'] - $stats['total_expenses'] - $stats['total_partner_payouts'];

        return view('admin.reports.index', compact('stats'));
    }

    public function dashboard()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todayRevenue = Order::whereDate('created_at', $today)->sum('total_amount');

        $monthOrders = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $monthRevenue = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total_amount');

        $lastMonthOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $lastMonthRevenue = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('total_amount');

        $totalProducts = Product::count();
        $totalCustomers = User::where('is_admin', false)->count();
        $totalPartners = Partner::count();

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $topProducts = Product::withCount('orderItems')->orderBy('order_items_count', 'desc')->take(5)->get();

        return view('admin.reports.dashboard', compact(
            'todayOrders', 'todayRevenue',
            'monthOrders', 'monthRevenue',
            'lastMonthOrders', 'lastMonthRevenue',
            'totalProducts', 'totalCustomers', 'totalPartners',
            'recentOrders', 'topProducts'
        ));
    }

    public function sales(Request $request)
    {
        $startDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : Carbon::now()->endOfMonth();

        $query = Order::whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->with('user', 'items')->latest()->paginate(15);

        // Calculate stats for the entire period (not filtered)
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->count();

        return view('admin.reports.sales', compact(
            'orders', 'totalRevenue', 'totalOrders', 'averageOrderValue', 'completedOrders',
            'startDate', 'endDate'
        ));
    }

    public function products(Request $request)
    {
        $query = Product::with(['category']);

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(20);

        // Calculate stats
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $lowStockProducts = Product::whereBetween('stock_quantity', [1, 10])->count();
        $outOfStockProducts = Product::where('stock_quantity', '<=', 0)->count();

        return view('admin.reports.products', compact('products', 'totalProducts', 'activeProducts', 'lowStockProducts', 'outOfStockProducts'));
    }

    public function customers(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $customers = User::where('is_admin', false)
            ->withCount(['orders' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['orders' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total_amount')
            ->orderBy('orders_count', 'desc')
            ->paginate(15);

        return view('admin.reports.customers', compact('customers', 'startDate', 'endDate'));
    }

    public function partners(Request $request)
    {
        $startDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : Carbon::now()->endOfMonth();

        $partners = Partner::with('payments')->paginate(15);

        // Calculate stats
        $totalPaid = \App\Models\PartnerPayment::where('status', 'completed')->sum('amount');
        $pendingPayments = \App\Models\PartnerPayment::where('status', 'pending')->sum('amount');

        return view('admin.reports.partners', compact('partners', 'totalPaid', 'pendingPayments'));
    }

    public function expenses(Request $request)
    {
        $startDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : Carbon::now()->endOfMonth();

        $query = Expense::whereBetween('expense_date', [$startDate, $endDate])->with(['category', 'partner']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);

        // Calculate stats
        $totalAmount = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->sum('amount');
        $pendingAmount = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'pending')
            ->sum('amount');

        // Get categories for filter
        $categories = \App\Models\ExpenseCategory::all();

        return view('admin.reports.expenses', compact('expenses', 'totalAmount', 'pendingAmount', 'categories'));
    }

    public function inventory(Request $request)
    {
        $query = Product::with(['category']);

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Apply stock status filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('stock_quantity', [1, 10]);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        $products = $query->paginate(20);

        // Calculate stats
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock_quantity');
        $lowStockCount = Product::whereBetween('stock_quantity', [1, 10])->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();
        $totalValue = Product::selectRaw('SUM(stock_quantity * price)')->value('SUM(stock_quantity * price)') ?? 0;

        return view('admin.reports.inventory', compact('products', 'totalProducts', 'totalStock', 'lowStockCount', 'outOfStockCount', 'totalValue'));
    }

    public function financial(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $report = $this->financialService->generateReport($startDate, $endDate);

        return view('admin.reports.financial', compact('report', 'startDate', 'endDate'));
    }

    public function exportSales(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->get();

        return response()->streamDownload(function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order ID', 'Customer', 'Email', 'Total', 'Status', 'Date']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user?->name,
                    $order->user?->email,
                    $order->total_amount,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        }, 'sales-report-' . now()->format('Y-m-d') . '.csv');
    }

    public function productProfit(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Get products with their order items in the date range
        $products = Product::with(['orderItems' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'delivered');
            })->with('order');
        }])->get();

        // Calculate profit for each product
        $report = $products->map(function ($product) {
            $totalRevenue = 0;
            $totalCost = 0;
            $quantitySold = 0;

            foreach ($product->orderItems as $item) {
                $quantitySold += $item->quantity;
                $totalRevenue += $item->price * $item->quantity;
                $totalCost += $item->cost_price * $item->quantity; // Assuming cost_price is stored on order_items
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category_name' => $product->category?->name ?? 'N/A',
                'quantity_sold' => $quantitySold,
                'revenue' => $totalRevenue,
                'cost' => $totalCost,
                'profit' => $totalRevenue - $totalCost,
            ];
        })->filter(function ($item) {
            return $item['quantity_sold'] > 0; // Only show products that were sold
        })->sortByDesc('profit')->values();

        // Calculate totals
        $totalRevenue = $report->sum('revenue');
        $totalCost = $report->sum('cost');
        $totalProfit = $report->sum('profit');
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        return view('admin.reports.product-profit', compact(
            'report', 'startDate', 'endDate',
            'totalRevenue', 'totalCost', 'totalProfit', 'profitMargin'
        ));
    }

    public function categoryProfit(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();
        $categoryId = $request->filled('category_id') ? $request->category_id : null;

        // Get categories for filter
        $categories = \App\Models\Category::all();
        $selectedCategory = $categoryId ? \App\Models\Category::find($categoryId) : null;

        // Get products filtered by category if specified
        $query = Product::with(['orderItems' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'delivered');
            });
        }]);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();

        // Calculate profit for each product
        $topProducts = $products->map(function ($product) {
            $totalRevenue = 0;
            $totalCost = 0;
            $quantitySold = 0;

            foreach ($product->orderItems as $item) {
                $quantitySold += $item->quantity;
                $totalRevenue += $item->price * $item->quantity;
                $totalCost += $item->cost_price * $item->quantity;
            }

            $profit = $totalRevenue - $totalCost;

            return [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity_sold' => $quantitySold,
                'revenue' => $totalRevenue,
                'cost' => $totalCost,
                'profit' => $profit,
            ];
        })->filter(function ($item) {
            return $item['quantity_sold'] > 0;
        })->sortByDesc('profit')->take(20)->values();

        // Calculate overall report
        $report = [
            'total_revenue' => $topProducts->sum('revenue'),
            'total_cost' => $topProducts->sum('cost'),
            'gross_profit' => $topProducts->sum('profit'),
            'top_products' => $topProducts,
        ];

        return view('admin.reports.category-profit', compact(
            'report', 'startDate', 'endDate', 'categories', 'selectedCategory'
        ));
    }
}
