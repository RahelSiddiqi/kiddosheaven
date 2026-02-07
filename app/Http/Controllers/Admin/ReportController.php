<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Investment;
use App\Models\Partner;
use App\Models\PartnerPayment;
use App\Models\PartnerCalculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function expenses(Request $request)
    {
        $query = Expense::with(['category', 'partner']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $categories = ExpenseCategory::all();

        // Calculate totals
        $totalAmount = $expenses->where('status', 'approved')->sum('amount');
        $pendingAmount = $expenses->where('status', 'pending')->sum('amount');

        // Category breakdown
        $categoryBreakdown = ExpenseCategory::with(['expenses' => function ($query) use ($request) {
            $query->where('status', 'approved');
            if ($request->filled('from_date')) {
                $query->whereDate('expense_date', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('expense_date', '<=', $request->to_date);
            }
        }])->get();

        return view('admin.reports.expenses', compact(
            'expenses',
            'categories',
            'totalAmount',
            'pendingAmount',
            'categoryBreakdown'
        ));
    }

    public function partners(Request $request)
    {
        $partners = Partner::with(['payments', 'calculations'])->get();

        $totalPaid = PartnerPayment::where('status', 'completed')
            ->when($request->filled('from_date'), function ($query) use ($request) {
                $query->whereDate('payment_date', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($query) use ($request) {
                $query->whereDate('payment_date', '<=', $request->to_date);
            })
            ->sum('amount');

        $pendingPayments = PartnerCalculation::where('status', 'approved')->sum('payment_amount');

        $partnerStats = $partners->map(function ($partner) {
            return [
                'partner' => $partner,
                'total_paid' => $partner->payments()->where('status', 'completed')->sum('amount'),
                'pending' => $partner->calculations()->where('status', 'approved')->sum('payment_amount'),
            ];
        });

        return view('admin.reports.partners', compact(
            'partners',
            'totalPaid',
            'pendingPayments',
            'partnerStats'
        ));
    }

    public function investments(Request $request)
    {
        $query = Investment::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $investments = $query->orderBy('investment_date', 'desc')->get();

        $totalInvested = $investments->sum('amount');
        $totalExpectedReturn = $investments->sum('expected_return');
        $totalActualReturn = $investments->where('status', 'completed')->sum('actual_return');

        $roi = $totalInvested > 0 ? (($totalActualReturn - $totalInvested) / $totalInvested) * 100 : 0;

        $typeBreakdown = Investment::select('type', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('type')
            ->pluck('total_amount', 'type');

        return view('admin.reports.investments', compact(
            'investments',
            'totalInvested',
            'totalExpectedReturn',
            'totalActualReturn',
            'roi',
            'typeBreakdown'
        ));
    }

    public function profitLoss(Request $request)
    {
        // Get date range
        $fromDate = $request->filled('from_date') ? $request->from_date : now()->startOfMonth()->toDateString();
        $toDate = $request->filled('to_date') ? $request->to_date : now()->endOfMonth()->toDateString();

        // Calculate total revenue (from orders)
        $totalRevenue = \App\Models\Order::where('status', 'delivered')
            ->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59'])
            ->sum('total_amount');

        // Calculate total expenses
        $totalExpenses = Expense::where('status', 'approved')
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->sum('amount');

        // Calculate partner payments
        $partnerPayments = PartnerPayment::where('status', 'completed')
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->sum('amount');

        // Calculate investments
        $totalInvestments = Investment::whereBetween('investment_date', [$fromDate, $toDate])
            ->sum('amount');

        // Net profit/loss
        $grossProfit = $totalRevenue - $totalExpenses - $partnerPayments;
        $netProfit = $grossProfit - $totalInvestments;

        // Expense breakdown by category
        $expenseBreakdown = ExpenseCategory::with(['expenses' => function ($query) use ($fromDate, $toDate) {
            $query->where('status', 'approved')
                  ->whereBetween('expense_date', [$fromDate, $toDate]);
        }])->get()->map(function ($category) {
            return [
                'name' => $category->name,
                'amount' => $category->expenses->sum('amount'),
                'color' => $category->color,
            ];
        })->sortByDesc('amount');

        return view('admin.reports.profit-loss', compact(
            'fromDate',
            'toDate',
            'totalRevenue',
            'totalExpenses',
            'partnerPayments',
            'totalInvestments',
            'grossProfit',
            'netProfit',
            'expenseBreakdown'
        ));
    }

    public function sales(Request $request)
    {
        $fromDate = $request->filled('from_date') ? $request->from_date : now()->startOfMonth()->toDateString();
        $toDate = $request->filled('to_date') ? $request->to_date : now()->endOfMonth()->toDateString();

        $query = \App\Models\Order::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $orders->where('payment_status', 'paid')->sum('total_amount');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return view('admin.reports.sales', compact(
            'fromDate',
            'toDate',
            'orders',
            'totalSales',
            'totalOrders',
            'avgOrderValue'
        ));
    }

    public function products(Request $request)
    {
        $query = \App\Models\Product::query();

        if ($request->filled('category')) {
            $query->where('catalog_id', $request->category);
        }

        if ($request->filled('status')) {
            $status = $request->status === 'active' ? 'active' : 'inactive';
            $query->where('status', $status);
        }

        $products = $query->with('catalog')->orderBy('created_at', 'desc')->get();

        $totalProducts = $products->count();
        $activeProducts = $products->where('status', 'active')->count();
        $lowStockProducts = $products->filter(function ($p) {
            return $p->stock <= 10 && $p->stock > 0;
        })->count();
        $outOfStockProducts = $products->where('stock_quantity', 0)->count();

        $totalStockValue = $products->sum(function ($p) {
            return $p->stock * $p->price;
        });

        return view('admin.reports.products', compact(
            'products',
            'totalProducts',
            'activeProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'totalStockValue'
        ));
    }

    public function inventory(Request $request)
    {
        $query = \App\Models\Product::query();

        if ($request->filled('category')) {
            $query->where('catalog_id', $request->category);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereBetween('stock_quantity', [1, 10]);
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock_quantity', '>', 10);
            }
        }

        $products = $query->with('catalog')->orderBy('stock_quantity', 'asc')->get();

        $totalProducts = $products->count();
        $totalStock = $products->sum('stock_quantity');
        $totalValue = $products->sum(function ($p) {
            return $p->stock_quantity * $p->price;
        });
        $lowStockCount = $products->whereBetween('stock_quantity', [1, 10])->count();
        $outOfStockCount = $products->where('stock_quantity', 0)->count();

        return view('admin.reports.inventory', compact(
            'products',
            'totalProducts',
            'totalStock',
            'totalValue',
            'lowStockCount',
            'outOfStockCount'
        ));
    }
}
