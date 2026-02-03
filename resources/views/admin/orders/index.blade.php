@extends('admin.layout')

@section('title', 'Orders — Admin')

@section('content')
	<div class="bg-white rounded-xl shadow p-6 mb-8">
		<div class="flex items-center justify-between border-b pb-4 mb-4">
			<h2 class="text-xl font-bold text-[color:var(--admin-primary-dark)]">Orders</h2>
		</div>

		@if ($orders->isEmpty())
			<p>No orders yet.</p>
		@else
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead>
						<tr class="bg-[color:var(--admin-bg)] text-[color:var(--admin-primary-dark)]">
							<th class="py-2 px-3 font-semibold">Order ID</th>
							<th class="py-2 px-3 font-semibold">Customer</th>
							<th class="py-2 px-3 font-semibold">Phone</th>
							<th class="py-2 px-3 font-semibold">Items</th>
							<th class="py-2 px-3 font-semibold">Total</th>
							<th class="py-2 px-3 font-semibold">Status</th>
							<th class="py-2 px-3 font-semibold">Date</th>
							<th class="py-2 px-3 font-semibold">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($orders as $order)
							<tr class="border-b last:border-0">
								<td class="py-2 px-3">#{{ $order->id }}</td>
								<td class="py-2 px-3">{{ $order->customer_name }}</td>
								<td class="py-2 px-3">{{ $order->customer_phone }}</td>
								<td class="py-2 px-3">{{ $order->items->sum('quantity') }} items</td>
								<td class="py-2 px-3">${{ number_format($order->total_amount / 100, 2) }}</td>
								<td class="py-2 px-3">
									<span
										class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold">{{ ucfirst($order->status) }}</span>
								</td>
								<td class="py-2 px-3">{{ $order->created_at->format('M d, Y') }}</td>
								<td class="py-2 px-3">
									<a href="{{ route('admin.orders.show', $order) }}"
										class="inline-flex items-center px-2 py-1 rounded bg-[color:var(--admin-bg)] text-[color:var(--admin-primary-dark)] border border-[color:var(--admin-primary)] hover:bg-[color:var(--admin-primary)] hover:text-white transition text-xs">View</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="mt-6">
				{{ $orders->links('vendor.pagination.default') }}
			</div>
		@endif
	</div>
@endsection
