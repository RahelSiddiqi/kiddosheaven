<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Domains\Order\Models\Order;
use App\Domains\Product\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Dashboard - Admin')]
class Dashboard extends Component
{
    public function render()
    {
        // Stats
        $totalRevenue = Order::whereNotIn('status', ['cancelled'])
            ->withoutGlobalScope('site')
            ->sum('total_amount');

        $totalOrders = Order::withoutGlobalScope('site')->count();
        $totalCustomers = User::where('is_admin', false)->count();
        $lowStockCount = Product::withoutGlobalScope('site')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 10)
            ->where('stock_quantity', '>', 0)
            ->count();

        $recentOrders = Order::withoutGlobalScope('site')
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $monthlySales = Order::withoutGlobalScope('site')
            ->whereNotIn('status', ['cancelled'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        // Order status breakdown for pie chart
        $orderStatusBreakdown = Order::withoutGlobalScope('site')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top 5 categories by revenue (join through order_items and products)
        try {
            $topCategories = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereNotIn('orders.status', ['cancelled'])
                ->select('categories.name', DB::raw('SUM(order_items.subtotal) as revenue'))
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('revenue')
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {
            $topCategories = collect();
        }

        return view('livewire.admin.dashboard.dashboard', compact(
            'totalRevenue', 'totalOrders', 'totalCustomers', 'lowStockCount',
            'recentOrders', 'monthlySales', 'orderStatusBreakdown', 'topCategories'
        ));
    }
}
