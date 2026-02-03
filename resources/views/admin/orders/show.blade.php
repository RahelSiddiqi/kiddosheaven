@extends('admin.layout')

@section('title', 'Order #' . $order->id . ' — Admin')

@section('content')
	<div class="bg-white rounded-xl shadow p-6 mb-8">
		<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b pb-4 mb-4 gap-4">
			<h2 class="text-xl font-bold text-[color:var(--admin-primary-dark)]">Order #{{ $order->id }}</h2>
			<a href="{{ route('admin.orders.index') }}"
				class="inline-flex items-center px-4 py-2 rounded bg-[color:var(--admin-bg)] text-[color:var(--admin-primary-dark)] border border-[color:var(--admin-primary)] hover:bg-[color:var(--color-primary-dark)] hover:text-white transition text-sm">Back
				to Orders</a>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
			<div>
				<h3 class="mb-3 text-base font-semibold text-gray-700">Customer Information</h3>
				<p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
				<p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
				@if ($order->customer_email)
					<p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
				@endif
			</div>
			<div>
				<h3 class="mb-3 text-base font-semibold text-gray-700">Delivery Address</h3>
				<p class="mb-1">{{ $order->address_line }}</p>
				<p class="mb-1">{{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}</p>
			</div>
			<div>
				<h3 class="mb-3 text-base font-semibold text-gray-700">Order Details</h3>
				<p class="mb-1"><strong>Status:</strong>
					<span
						class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-semibold">{{ ucfirst($order->status) }}</span>
				</p>
				<p class="mb-1"><strong>Payment:</strong> {{ strtoupper($order->payment_method) }}</p>
				<p class="mb-1"><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
			</div>
		</div>

		@if ($order->notes)
			<div class="mb-6 p-3 bg-gray-50 rounded-lg">
				<strong>Notes:</strong> {{ $order->notes }}
			</div>
		@endif

		<h3 class="mb-3 text-base font-semibold text-gray-700">Order Items</h3>
		<div class="overflow-x-auto">
			<table class="min-w-full text-sm mb-4">
				<thead>
					<tr class="bg-[color:var(--admin-bg)] text-[color:var(--admin-primary-dark)]">
						<th class="py-2 px-3 font-semibold">Product</th>
						<th class="py-2 px-3 font-semibold">Quantity</th>
						<th class="py-2 px-3 font-semibold">Unit Price</th>
						<th class="py-2 px-3 font-semibold">Total</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($order->items as $item)
						<tr class="border-b last:border-0">
							<td class="py-2 px-3">{{ $item->product->name }}</td>
							<td class="py-2 px-3">{{ $item->quantity }}</td>
							<td class="py-2 px-3">${{ number_format($item->unit_price / 100, 2) }}</td>
							<td class="py-2 px-3">${{ number_format($item->total_price / 100, 2) }}</td>
						</tr>
					@endforeach
					<tr class="font-semibold bg-gray-50">
						<td colspan="3" class="text-right py-2 px-3">Total Amount:</td>
						<td class="py-2 px-3">${{ number_format($order->total_amount / 100, 2) }}</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="mt-8 pt-6 border-t">
			<h3 class="mb-3 text-base font-semibold text-gray-700">Update Order Status</h3>
			<form action="{{ route('admin.orders.updateStatus', $order) }}" method="post"
				class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-start sm:items-center">
				@csrf
				<select name="status"
					class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[color:var(--color-primary-dark)] focus:border-transparent">
					<option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
					<option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
					<option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
					<option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
					<option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
				</select>
				<button type="submit"
					class="inline-flex items-center px-4 py-2 rounded bg-gradient-to-br from-[color:var(--color-primary-dark)] focus:border-transparent to-[color:var(--color-primary-dark)] cursor-pointer text-white font-bold shadow hover:from-[color:var(--color-primary-dark)] transition">Update
					Status</button>
			</form>
		</div>
	</div>
@endsection
