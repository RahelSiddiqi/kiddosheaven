@extends('admin.layout')

@section('title', 'Dashboard — Admin')

@section('content')
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
		<div
			class="flex items-center bg-gradient-to-br from-[var(--color-primary-dark)] to-[var(--color-primary-dark)] text-white rounded-xl p-6 shadow-md">
			<span class="text-3xl mr-4">📦</span>
			<div>
				<div class="text-sm opacity-90 font-medium">Total Products</div>
				<div class="text-2xl font-bold tracking-wide">{{ $stats['total_products'] }}</div>
			</div>
		</div>
		<div
			class="flex items-center bg-gradient-to-br from-[var(--color-primary-dark)] to-[var(--color-primary-dark)] text-white rounded-xl p-6 shadow-md">
			<span class="text-3xl mr-4">🧾</span>
			<div>
				<div class="text-sm opacity-90 font-medium">Total Orders</div>
				<div class="text-2xl font-bold tracking-wide">{{ $stats['total_orders'] }}</div>
			</div>
		</div>
		<div
			class="flex items-center bg-gradient-to-br from-[var(--color-primary-dark)] to-[var(--color-primary-dark)] text-white rounded-xl p-6 shadow-md">
			<span class="text-3xl mr-4">⏳</span>
			<div>
				<div class="text-sm opacity-90 font-medium">Pending Orders</div>
				<div class="text-2xl font-bold tracking-wide">{{ $stats['pending_orders'] }}</div>
			</div>
		</div>
		<div
			class="flex items-center bg-gradient-to-br from-[var(--color-primary-dark)] to-[var(--color-primary-dark)] text-white rounded-xl p-6 shadow-md">
			<span class="text-3xl mr-4">💰</span>
			<div>
				<div class="text-sm opacity-90 font-medium">Total Revenue</div>
				<div class="text-2xl font-bold tracking-wide">${{ number_format($stats['total_revenue'], 2) }}</div>
			</div>
		</div>
	</div>

	<div class="bg-white rounded-xl shadow p-6 mb-8">
		<div class="flex justify-between items-center border-b pb-4 mb-4">
			<h2 class="text-xl font-bold text-[var(--color-primary-dark)]">Recent Orders</h2>
			<a href="{{ route('admin.orders.index') }}"
				class="inline-flex items-center px-4 py-2 rounded bg-[color:var(--color-primary)] text-[color:white] border border-[color:var(--color-primary-dark)] hover:bg-[color:var(--color-primary-dark)] hover:text-white transition">View
				All</a>
		</div>
		@if ($recentOrders->isEmpty())
			<p>No orders yet.</p>
		@else
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead>
						<tr class="bg-[--admin-bg] text-[var(--color-primary-dark)]">
							<th class="py-2 px-3 font-semibold">Order ID</th>
							<th class="py-2 px-3 font-semibold">Customer</th>
							<th class="py-2 px-3 font-semibold">Items</th>
							<th class="py-2 px-3 font-semibold">Total</th>
							<th class="py-2 px-3 font-semibold">Status</th>
							<th class="py-2 px-3 font-semibold">Date</th>
							<th class="py-2 px-3 font-semibold">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($recentOrders as $order)
							<tr class="border-b last:border-0">
								<td class="py-2 px-3">#{{ $order->id }}</td>
								<td class="py-2 px-3">{{ $order->customer_name }}</td>
								<td class="py-2 px-3">{{ $order->items->sum('quantity') }} items</td>
								<td class="py-2 px-3">${{ number_format($order->total_amount / 100, 2) }}</td>
								<td class="py-2 px-3">
									<span
										class="inline-block px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-medium">{{ ucfirst($order->status) }}</span>
								</td>
								<td class="py-2 px-3">{{ $order->created_at->format('M d, Y') }}</td>
								<td class="py-2 px-3">
									<a href="{{ route('admin.orders.show', $order) }}"
										class="inline-flex items-center px-2 py-1 rounded bg-[--admin-bg] text-[--admin-primary-dark] border border-[--admin-primary] hover:bg-[var(--color-primary-dark)] hover:text-white transition text-xs">View</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>
@endsection
