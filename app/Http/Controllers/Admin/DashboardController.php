<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Date ranges
        $today = now()->startOfDay();
        $thisWeekStart = now()->startOfWeek();
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        // Revenue stats
        $totalRevenue = Order::sum('total_amount') / 100;
        $revenueToday = Order::where('created_at', '>=', $today)->sum('total_amount') / 100;
        $revenueThisWeek = Order::where('created_at', '>=', $thisWeekStart)->sum('total_amount') / 100;
        $revenueThisMonth = Order::where('created_at', '>=', $thisMonthStart)->sum('total_amount') / 100;

        // Revenue last month (for comparison)
        $revenueLastMonth = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('total_amount') / 100;
        $revenueGrowth = $revenueLastMonth > 0
            ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100
            : 0;

        // Order stats
        $ordersToday = Order::where('created_at', '>=', $today)->count();
        $ordersThisWeek = Order::where('created_at', '>=', $thisWeekStart)->count();
        $ordersThisMonth = Order::where('created_at', '>=', $thisMonthStart)->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // Average order value
        $totalOrders = Order::count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Customer stats
        $totalCustomers = User::where('is_admin', false)->count();
        $newCustomersThisMonth = User::where('is_admin', false)
            ->where('created_at', '>=', $thisMonthStart)
            ->count();
        $activeCustomers = User::where('is_admin', false)
            ->where('is_active', true)
            ->count();

        // Inventory stats
        $lowStockCount = Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 10)
            ->count();
        $outOfStockCount = Product::where('stock_quantity', 0)->count();
        $totalProducts = Product::count();

        // Stats array for view (associative for easy access)
        $stats = [
            'totalRevenue' => $totalRevenue,
            'revenueChange' => ($revenueGrowth >= 0 ? '+' : '') . number_format($revenueGrowth, 1) . '%',
            'revenueChangeType' => $revenueGrowth >= 0 ? 'positive' : 'negative',
            'ordersThisMonth' => $ordersThisMonth,
            'ordersChange' => null,
            'ordersChangeType' => null,
            'totalCustomers' => $totalCustomers,
            'customersChange' => '+' . number_format($newCustomersThisMonth) . ' new',
            'customersChangeType' => 'positive',
            'lowStockCount' => $lowStockCount,
        ];

        // Sales chart data (last 30 days)
        $salesChartData = $this->getSalesChartData(30);

        // Orders by status chart
        $ordersByStatus = $this->getOrdersByStatusData();

        // Top products
        $topProducts = Product::with('brand')
            ->orderBy('sold_count', 'desc')
            ->take(5)
            ->get();

        // Low stock items
        $lowStockItems = Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity', 'asc')
            ->take(10)
            ->get();

        // Recent orders
        $recentOrders = Order::with('items.product')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'salesChartData',
            'ordersByStatus',
            'topProducts',
            'recentOrders',
            'lowStockItems'
        ));
    }

    /**
     * Get sales chart data for last N days
     */
    private function getSalesChartData(int $days): array
    {
        $labels = [];
        $revenueData = [];
        $ordersData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');

            $revenue = Order::whereDate('created_at', $date->toDateString())
                ->sum('total_amount') / 100;
            $revenueData[] = $revenue;

            $ordersData[] = Order::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'orders' => $ordersData,
        ];
    }

    /**
     * Get orders by status data for chart
     */
    private function getOrdersByStatusData(): array
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $colors = [
            'pending' => 'hsl(48 96% 53%)',
            'processing' => 'hsl(221.2 83.2% 53.3%)',
            'shipped' => 'hsl(262 83% 58%)',
            'delivered' => 'hsl(142 71% 45%)',
            'cancelled' => 'hsl(0 84.2% 60.2%)',
        ];

        $data = [];
        foreach ($statuses as $status) {
            $data[] = Order::where('status', $status)->count();
        }

        return [
            'labels' => array_map('ucfirst', $statuses),
            'data' => $data,
            'colors' => array_values($colors),
        ];
    }
}
