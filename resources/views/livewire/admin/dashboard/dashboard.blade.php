<div>
    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-admin.ui.stat-card
            title="Total Revenue"
            :value="number_format($totalRevenue, 0)"
            subtitle="all time"
            icon="currency"
            color="green"
        />
        <x-admin.ui.stat-card
            title="Total Orders"
            :value="$totalOrders"
            subtitle="orders placed"
            icon="cart"
            color="blue"
        />
        <x-admin.ui.stat-card
            title="Total Customers"
            :value="$totalCustomers"
            subtitle="registered users"
            icon="users"
            color="purple"
        />
        <x-admin.ui.stat-card
            title="Low Stock Alerts"
            :value="$lowStockCount"
            subtitle="items need restocking"
            icon="alert"
            :color="$lowStockCount > 0 ? 'orange' : 'green'"
        />
    </div>

    {{-- Recent Orders & Monthly Sales --}}
    <div class="grid grid-cols-12 gap-4 md:gap-6">
        {{-- Recent Orders --}}
        <div class="col-span-12 xl:col-span-7">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Recent Orders</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400">
                        View All
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Order #</th>
                                <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Customer</th>
                                <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Total</th>
                                <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-gray-900 hover:text-brand-600 dark:text-white dark:hover:text-brand-400">
                                            #{{ $order->order_number ?? $order->id }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $order->customer_name ?? ($order->user->name ?? 'Guest') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer_email ?? ($order->user->email ?? '-') }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($order->total_amount, 0) }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                'shipped' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                                'delivered' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No orders yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Monthly Revenue Chart --}}
        <div class="col-span-12 xl:col-span-5">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Revenue — Last 6 Months</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Monthly trend</span>
                </div>
                <div class="p-5">
                    <canvas id="revenueChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Status & Top Categories Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- Order Status Chart --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Order Status</h3>
            </div>
            <div class="p-5 flex items-center justify-center">
                <canvas id="statusChart" width="220" height="220"></canvas>
            </div>
        </div>

        {{-- Top Categories --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Top Categories</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">By revenue</span>
            </div>
            <div class="p-5">
                <canvas id="categoryChart" height="140"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Revenue Line Chart ──────────────────────────────
    const revenueData = @json($monthlySales->map(fn($s) => [
        'label' => \Carbon\Carbon::createFromDate($s->year, $s->month)->format('M Y'),
        'revenue' => (float) $s->revenue,
        'orders' => (int) $s->orders,
    ]));

    if (document.getElementById('revenueChart')) {
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: revenueData.map(d => d.label),
                datasets: [{
                    label: 'Revenue (৳)',
                    data: revenueData.map(d => d.revenue),
                    borderColor: '#018790',
                    backgroundColor: 'rgba(1,135,144,0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#018790',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => '৳' + v.toLocaleString() },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ── Order Status Doughnut ───────────────────────────
    const statusData = @json($orderStatusBreakdown);
    const statusColors = {
        'pending': '#f59e0b', 'processing': '#3b82f6',
        'shipped': '#8b5cf6', 'delivered': '#10b981',
        'completed': '#10b981', 'cancelled': '#ef4444',
        'refunded': '#f97316',
    };

    if (document.getElementById('statusChart') && Object.keys(statusData).length) {
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: Object.keys(statusData).map(s => statusColors[s] ?? '#6b7280'),
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, font: { size: 12 } } }
                }
            }
        });
    }

    // ── Top Categories Bar Chart ────────────────────────
    const catData = @json($topCategories);

    if (document.getElementById('categoryChart') && catData.length) {
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: catData.map(c => c.name),
                datasets: [{
                    label: 'Revenue',
                    data: catData.map(c => parseFloat(c.revenue)),
                    backgroundColor: ['#018790','#00b7b5','#005461','#f59e0b','#8b5cf6'],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { callback: v => '৳' + v.toLocaleString() },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endpush
