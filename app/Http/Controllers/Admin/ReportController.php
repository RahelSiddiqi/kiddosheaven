<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Partner;
use App\Models\Expense;
use App\Models\InventoryMovement;
use App\Models\Investor;
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

        $startDate = request('start_date') ? Carbon::parse(request('start_date')) : Carbon::now()->startOfMonth();
        $endDate = request('end_date') ? Carbon::parse(request('end_date')) : Carbon::now()->endOfMonth();

        return view('admin.reports.index', compact('stats', 'startDate', 'endDate'));
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
        $statuses = [
            \App\Models\Expense::STATUS_PENDING => 'Pending',
            \App\Models\Expense::STATUS_APPROVED => 'Approved',
            \App\Models\Expense::STATUS_REJECTED => 'Rejected',
        ];

        return view('admin.reports.expenses', compact('expenses', 'totalAmount', 'pendingAmount', 'categories', 'statuses'));
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

    /**
     * Cost history report: purchase and movement history with unit costs over time.
     */
    public function costHistory(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->subMonths(3)->startOfDay();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        $query = InventoryMovement::with(['product', 'batch'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('unit_cost')
            ->orderByDesc('created_at');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $movements = $query->paginate(25)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('admin.reports.cost-history', compact('movements', 'products', 'startDate', 'endDate'));
    }

    /**
     * Batch stock report: View stock levels by purchase batch with FIFO costing
     */
    public function batchStock(Request $request)
    {
        $products = Product::orderBy('name')->get(['id', 'name']);
        $report = null;

        if ($request->filled('product_id')) {
            $product = Product::find($request->product_id);

            if ($product) {
                $batches = \App\Models\PurchaseBatch::where('product_id', $product->id)
                    ->orderBy('purchase_date')
                    ->get();

                $totalQuantity = $batches->sum('quantity');
                $remainingQuantity = $batches->sum('remaining_quantity');
                $totalValue = $batches->sum(fn($b) => $b->remaining_quantity * $b->unit_cost);

                $report = [
                    'product' => $product,
                    'batches' => $batches,
                    'total_quantity' => $totalQuantity,
                    'remaining_quantity' => $remainingQuantity,
                    'total_value' => $totalValue,
                ];
            }
        }

        return view('admin.reports.batch-stock', compact('products', 'report'));
    }

    /**
     * Export batch stock report to CSV
     */
    public function exportBatchStock(Request $request)
    {
        $productId = $request->get('product_id');

        if (!$productId) {
            return redirect()->route('admin.reports.batch-stock')->with('error', 'Please select a product first.');
        }

        $product = Product::find($productId);
        $batches = \App\Models\PurchaseBatch::where('product_id', $productId)
            ->orderBy('purchase_date')
            ->get();

        return response()->streamDownload(function () use ($batches, $product) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Batch Number', 'Purchase Date', 'Unit Cost', 'Received', 'Remaining', 'Sold', 'Batch Value', 'Expiry Date']);

            foreach ($batches as $batch) {
                $sold = $batch->quantity - $batch->remaining_quantity;
                $batchValue = $batch->remaining_quantity * $batch->unit_cost;

                fputcsv($file, [
                    $batch->batch_number,
                    $batch->purchase_date ? Carbon::parse($batch->purchase_date)->format('Y-m-d') : '',
                    number_format($batch->unit_cost, 2),
                    $batch->quantity,
                    $batch->remaining_quantity,
                    $sold,
                    number_format($batchValue, 2),
                    $batch->expiry_date ? Carbon::parse($batch->expiry_date)->format('Y-m-d') : '',
                ]);
            }

            fclose($file);
        }, 'batch-stock-' . ($product ? $product->slug : 'all') . '-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Expiring items report
     */
    public function expiringItems(Request $request)
    {
        $days = $request->filled('days') ? (int) $request->days : 30;
        $expiryDate = now()->addDays($days);

        $batches = \App\Models\PurchaseBatch::with('product')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', $expiryDate)
            ->where('expiry_date', '>=', now())
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        return view('admin.reports.expiring', compact('batches', 'days'));
    }

    /**
     * Partner contribution report
     */
    public function partnerContribution(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $partners = Partner::all();
        $selectedPartner = $request->filled('partner_id') ? Partner::find($request->partner_id) : null;
        $report = null;

        if ($selectedPartner) {
            // Calculate partner's contribution and commission
            $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'delivered')
                ->with('items')
                ->get();

            $totalRevenue = $orders->sum('total_amount');
            $totalCost = $orders->sum(function ($order) {
                return $order->items->sum(function ($item) {
                    return ($item->cost_price ?? 0) * $item->quantity;
                });
            });

            $grossProfit = $totalRevenue - $totalCost;
            $commissionRate = $selectedPartner->commission_rate ?? 10; // Default 10% if not set
            $partnerCommission = ($grossProfit * $commissionRate) / 100;

            $report = [
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'gross_profit' => $grossProfit,
                'commission_rate' => $commissionRate,
                'partner_commission' => $partnerCommission,
                'total_orders' => $orders->count(),
            ];
        }

        return view('admin.reports.partner-contribution', compact('partners', 'selectedPartner', 'report', 'startDate', 'endDate'));
    }

    /**
     * Investor ROI report
     */
    public function investorROI(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Get all investors for dropdown
        $investors = Investor::orderBy('name')->get();
        $selectedInvestor = $request->filled('investor_id') ? Investor::find($request->investor_id) : null;
        $report = null;

        if ($selectedInvestor) {
            // Get investor's investments in the date range
            $investments = \App\Models\Investment::where('investor_id', $selectedInvestor->id)
                ->whereBetween('investment_date', [$startDate, $endDate])
                ->get();

            // Calculate totals (using current_value as returns since actual_return column doesn't exist)
            $totalInvestment = $investments->sum('amount');
            $totalReturns = $investments->sum('current_value') ?? $totalInvestment;
            $netProfit = $totalReturns - $totalInvestment;
            $roiPercentage = $totalInvestment > 0 ? ($netProfit / $totalInvestment) * 100 : 0;

            // Get revenue and costs for the period
            $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'delivered')
                ->with('items')
                ->get();

            $totalRevenue = $orders->sum('total_amount');
            $totalCosts = $orders->sum(function ($order) {
                return $order->items->sum(function ($item) {
                    return ($item->cost_price ?? 0) * $item->quantity;
                });
            });
            $grossProfit = $totalRevenue - $totalCosts;

            // Ownership percentage (assuming it's stored on investor or calculate based on total capital)
            $ownershipPercentage = $selectedInvestor->ownership_percentage ?? 0;

            $report = [
                'total_investment' => $totalInvestment,
                'total_returns' => $totalReturns,
                'net_profit' => $netProfit,
                'roi_percentage' => $roiPercentage,
                'ownership_percentage' => $ownershipPercentage,
                'opening_balance' => $investments->where('type', 'inventory')->sum('amount'),
                'additional_investment' => $investments->where('type', 'expansion')->sum('amount'),
                'withdrawals' => 0, // No withdrawal tracking in current schema
                'closing_balance' => $totalInvestment,
                'total_revenue' => $totalRevenue,
                'total_costs' => $totalCosts,
                'gross_profit' => $grossProfit,
            ];
        }

        return view('admin.reports.investor-roi', compact(
            'investors', 'selectedInvestor', 'report', 'startDate', 'endDate'
        ));
    }

    /**
     * Investments report
     */
    public function investments(Request $request)
    {
        $query = \App\Models\Investment::with(['investor', 'purchaseBatches']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $investments = $query->latest()->paginate(20);

        // Calculate totals with new tracking
        $totalInvested = \App\Models\Investment::sum('amount');
        $totalSpent = \App\Models\Investment::sum('spent_amount');
        $totalAvailable = $totalInvested - $totalSpent;
        $totalCurrentValue = \App\Models\Investment::sum('current_value');

        // Calculate inventory value from linked batches
        $totalInventoryValue = \App\Models\PurchaseBatch::whereNotNull('investment_id')
            ->get()
            ->sum(function ($batch) {
                return $batch->remaining_quantity * $batch->unit_cost;
            });

        // Use current_value as both expected and actual return
        $totalExpectedReturn = $totalCurrentValue;
        $totalActualReturn = $totalCurrentValue;

        // Calculate ROI based on current value vs invested amount
        $roi = $totalInvested > 0 ? (($totalCurrentValue - $totalInvested) / $totalInvested) * 100 : 0;

        return view('admin.reports.investments', compact(
            'investments', 'totalInvested', 'totalSpent', 'totalAvailable',
            'totalExpectedReturn', 'totalActualReturn', 'totalInventoryValue', 'roi'
        ));
    }

    /**
     * Profit & Loss report
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Revenue from delivered orders
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->sum('total_amount');

        // Cost of goods sold
        $cogs = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->with('items')
            ->get()
            ->sum(function ($order) {
                return $order->items->sum(function ($item) {
                    return ($item->cost_price ?? 0) * $item->quantity;
                });
            });

        // Operating expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->sum('amount');

        // Partner payments
        $partnerPayments = \App\Models\PartnerPayment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('amount');

        // Investments
        $totalInvestments = \App\Models\Investment::whereBetween('investment_date', [$startDate, $endDate])
            ->sum('amount');

        // Total expenses (COGS + Operating Expenses)
        $totalExpenses = $cogs + $expenses;

        // Expense breakdown by category
        $expenseBreakdown = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($items, $categoryName) {
                return [
                    'name' => $categoryName ?? 'Uncategorized',
                    'amount' => $items->sum('amount'),
                    'color' => $this->getCategoryColor($categoryName),
                ];
            })
            ->values();

        // Calculations
        $grossProfit = $totalRevenue - $totalExpenses - $partnerPayments;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        $netProfit = $grossProfit - $totalInvestments;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Format dates for form inputs
        $fromDate = $startDate->format('Y-m-d');
        $toDate = $endDate->format('Y-m-d');

        return view('admin.reports.profit-loss', compact(
            'startDate', 'endDate', 'fromDate', 'toDate',
            'totalRevenue', 'totalExpenses', 'partnerPayments', 'totalInvestments',
            'grossProfit', 'grossMargin', 'netProfit', 'netMargin',
            'expenseBreakdown'
        ));
    }

    /**
     * Get color for expense category
     */
    private function getCategoryColor($categoryName)
    {
        $colors = [
            'Salary' => '#10b981',
            'Rent' => '#3b82f6',
            'Utilities' => '#f59e0b',
            'Marketing' => '#ec4899',
            'Supplies' => '#8b5cf6',
            'Transportation' => '#14b8a6',
            'Maintenance' => '#f97316',
            'Insurance' => '#6366f1',
        ];

        return $colors[$categoryName] ?? '#6b7280';
    }

    /**
     * Financial summary report
     */
    public function financialSummary(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        // Revenue calculation
        $grossSales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->sum('total_amount');

        $returns = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['returned', 'refunded'])
            ->sum('total_amount');

        $netRevenue = $grossSales - $returns;

        // COGS (Cost of Goods Sold)
        $cogs = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->with('items')
            ->get()
            ->sum(function ($order) {
                return $order->items->sum(function ($item) {
                    return ($item->cost_price ?? 0) * $item->quantity;
                });
            });

        // Operating expenses
        $operatingExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->sum('amount');

        // Total costs
        $totalCosts = $cogs + $operatingExpenses;

        // Profitability
        $grossProfit = $netRevenue - $cogs;
        $netProfitValue = $netRevenue - $totalCosts;
        $profitMargin = $netRevenue > 0 ? ($netProfitValue / $netRevenue) * 100 : 0;

        // Build netProfit array for detailed breakdown
        $netProfit = [
            'gross_sales' => $grossSales,
            'returns' => $returns,
            'net_revenue' => $netRevenue,
            'cogs' => $cogs,
            'operating_expenses' => $operatingExpenses,
            'total_costs' => $totalCosts,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfitValue,
            'profit_margin' => $profitMargin,
        ];

        // Stock valuation (current inventory value)
        $stockValuation = \App\Models\Product::with('purchaseBatches')
            ->get()
            ->sum(function ($product) {
                return $product->purchaseBatches->sum(function ($batch) {
                    return $batch->remaining_quantity * $batch->unit_cost;
                });
            });

        // Partner payables
        $partnerPayables = \App\Models\Partner::where('status', 'active')
            ->withSum(['payments as total_paid' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('payment_date', [$startDate, $endDate])
                    ->where('status', 'completed');
            }], 'amount')
            ->get()
            ->map(function ($partner) {
                $partner->total_payable = ($partner->total_invested ?? 0) - ($partner->total_paid ?? 0);
                return $partner;
            });

        // Category breakdown
        $expensesByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn($items) => $items->sum('amount'));

        return view('admin.reports.financial-summary', compact(
            'startDate', 'endDate',
            'netProfit', 'stockValuation', 'partnerPayables',
            'expensesByCategory'
        ));
    }
}
