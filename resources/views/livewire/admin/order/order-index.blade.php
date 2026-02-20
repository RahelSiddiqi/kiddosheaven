<div>
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Orders</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage customer orders</p>
        </div>
        <a href="{{ route('admin.orders.export', request()->query()) }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export CSV
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <x-admin.ui.stat-card
            title="Total Orders"
            :value="$stats['total']"
            icon="cart"
            color="blue"
            :compact="true"
        />
        <x-admin.ui.stat-card
            title="Pending"
            :value="$stats['pending']"
            icon="clock"
            color="yellow"
            :compact="true"
        />
        <x-admin.ui.stat-card
            title="Processing"
            :value="$stats['processing']"
            icon="cart"
            color="blue"
            :compact="true"
        />
        <x-admin.ui.stat-card
            title="Shipped"
            :value="$stats['shipped']"
            icon="cart"
            color="purple"
            :compact="true"
        />
        <x-admin.ui.stat-card
            title="Delivered"
            :value="$stats['delivered']"
            icon="check"
            color="green"
            :compact="true"
        />
        <x-admin.ui.stat-card
            title="Revenue"
            :value="number_format($stats['revenue'], 0)"
            icon="currency"
            color="brand"
            :compact="true"
        />
    </div>

    {{-- Filters & Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-4 px-5 py-4 border-b border-gray-200 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Order List</h3>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                {{-- Bulk Actions --}}
                @if(count($selectedOrderIds) > 0)
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">{{ count($selectedOrderIds) }} selected</span>
                        <select
                            wire:model="bulkStatus"
                            class="h-8 rounded-lg border border-blue-300 bg-white px-2 text-sm text-gray-800 focus:border-blue-500 focus:outline-none dark:border-blue-700 dark:bg-gray-800 dark:text-white/90"
                        >
                            <option value="">Set Status...</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button
                            wire:click="bulkUpdateStatus"
                            wire:loading.attr="disabled"
                            class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 disabled:opacity-50 transition"
                        >
                            Apply
                        </button>
                    </div>
                @endif

                {{-- Search --}}
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search orders..."
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent pl-10 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 sm:w-64"
                    />
                </div>

                {{-- Status Filter --}}
                <select
                    wire:model.live="status"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>

                {{-- Sort --}}
                <select
                    wire:model.live="sort"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="total-high">Total: High to Low</option>
                    <option value="total-low">Total: Low to High</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        @if($orders->isEmpty())
            <div class="px-5 py-12 text-center">
                <svg class="mx-auto w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No orders found</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    @if($search || $status)
                        Try adjusting your filters or search term.
                    @else
                        Orders will appear here when customers place them.
                    @endif
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-3 text-left">
                                <input type="checkbox" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800" />
                            </th>
                            <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Order #</th>
                            <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Customer</th>
                            <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Total</th>
                            <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-sm font-normal text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-5 py-3 text-right text-sm font-normal text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]" wire:key="order-{{ $order->id }}">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <input type="checkbox" value="{{ $order->id }}" wire:model.live="selectedOrderIds" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800" />
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-gray-900 hover:text-brand-600 dark:text-white dark:hover:text-brand-400">
                                        #{{ $order->id }}
                                    </a>
                                    @if($order->order_number)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->order_number }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->customer_name ?? ($order->user->name ?? 'Guest') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->customer_email ?? ($order->user->email ?? '-') }}</div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($order->total_amount, 0) }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                        class="text-xs rounded-full border border-gray-300 px-2 py-1 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:outline-none">
                                        @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $order->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400"
                                            title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if(Route::has('admin.orders.invoice'))
                                            <a href="{{ route('admin.orders.invoice', $order) }}"
                                                class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400"
                                                title="Download Invoice">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
