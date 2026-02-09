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

    public function dashboard()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todayRevenue = Order::whereDate('created_at', $today)->sum('total');

        $monthOrders = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $monthRevenue = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total');

        $lastMonthOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $lastMonthRevenue = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('total');

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
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $query = Order::whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->with('user', 'items')->latest()->paginate(15);
        $totalRevenue = $query->sum('total');
        $totalOrders = $query->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $dailySales = $query->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.sales', compact(
            'orders', 'totalRevenue', 'totalOrders', 'averageOrderValue',
            'dailySales', 'startDate', 'endDate'
        ));
    }

    public function products(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $products = Product::withCount(['orderItems' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }])
        ->withSum(['orderItems' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }], 'quantity')
        ->withSum(['orderItems' => function ($query) use ($startDate, $endDate) {
            $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }], 'subtotal')
        ->get();

        return view('admin.reports.products', compact('products', 'startDate', 'endDate'));
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
            }], 'total')
            ->orderBy('orders_count', 'desc')
            ->paginate(15);

        return view('admin.reports.customers', compact('customers', 'startDate', 'endDate'));
    }

    public function partners(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $partners = Partner::withCount(['orders' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->withSum(['orders' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }], 'total')
        ->orderBy('orders_count', 'desc')
        ->paginate(15);

        return view('admin.reports.partners', compact('partners', 'startDate', 'endDate'));
    }

    public function expenses(Request $request)
    {
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $query = Expense::whereBetween('expense_date', [$startDate, $endDate]);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $expenses = $query->with('category', 'partner')->latest()->paginate(15);
        $totalExpenses = $query->sum('amount');

        $expensesByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category, SUM(expenses.amount) as total')
            ->groupBy('expense_categories.name')
            ->get();

        return view('admin.reports.expenses', compact('expenses', 'totalExpenses', 'expensesByCategory', 'startDate', 'endDate'));
    }

    public function inventory(Request $request)
    {
        $products = Product::with(['inventory', 'category', 'brand'])
            ->when($request->filled('low_stock'), function ($query) {
                $query->whereHas('inventory', function ($q) {
                    $q->whereRaw('quantity <= reorder_level');
                });
            })
            ->when($request->filled('out_of_stock'), function ($query) {
                $query->whereHas('inventory', function ($q) {
                    $q->where('quantity', 0);
                });
            })
            ->paginate(15);

        return view('admin.reports.inventory', compact('products'));
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
                    $order->total,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        }, 'sales-report-' . now()->format('Y-m-d') . '.csv');
    }
}
