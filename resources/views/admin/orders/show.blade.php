@extends('admin.layouts.app')

@section('title', 'Order #' . $order->id . ' — Kiddo\'s Heaven')

@section('content')
	{{-- Entity Header with Navigation Links --}}
	<x-admin.ui.entity-header
		:title="'Order #' . $order->id"
		:subtitle="'Placed on ' . $order->created_at->format('M d, Y H:i')"
		:badge="ucfirst($order->status)"
		:badgeColor="match($order->status) {
			'delivered' => 'green',
			'shipped' => 'purple',
			'processing' => 'blue',
			'cancelled' => 'red',
			default => 'orange'
		}"
		:breadcrumbs="[
			['label' => 'Dashboard', 'url' => route('admin.dashboard')],
			['label' => 'Orders', 'url' => route('admin.orders.index')],
			['label' => 'Order #' . $order->id],
		]"
		:actions="[
			['label' => 'Download Invoice', 'url' => route('admin.orders.invoice', $order), 'icon' => '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'/></svg>'],
		]"
		:links="[
			['label' => 'Order Details', 'url' => route('admin.orders.show', $order), 'active' => true],
			['label' => 'Customer', 'url' => $order->user ? route('admin.customers.show', $order->user) : '#'],
			['label' => 'Invoice', 'url' => route('admin.orders.invoice', $order)],
		]"
		backUrl="{{ route('admin.orders.index') }}"
	/>

	{{-- Stats Row --}}
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
		<x-admin.ui.stat-card
			title="Order Total"
			:value="'৳' . number_format($order->total_amount - $order->discount_amount, 0)"
			:subtitle="$order->items->sum('quantity') . ' items'"
			icon="currency"
			color="blue"
		/>
		<x-admin.ui.stat-card
			title="Subtotal"
			:value="'৳' . number_format($order->total_amount, 2)"
			:subtitle="$order->discount_amount > 0 ? 'Before discount' : 'No discounts applied'"
			icon="cart"
			:color="$order->discount_amount > 0 ? 'purple' : 'gray'"
		/>
		<x-admin.ui.stat-card
			title="Payment Method"
			:value="ucfirst($order->payment_method ?? 'N/A')"
			:subtitle="ucfirst($order->payment_status ?? 'pending')"
			icon="currency"
			:color="$order->payment_status === 'paid' ? 'green' : 'orange'"
		/>
		<x-admin.ui.stat-card
			title="Items"
			:value="$order->items->sum('quantity')"
			:subtitle="$order->items->count() . ' unique products'"
			icon="box"
			color="purple"
		/>
	</div>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Left Column - Main Content -->
		<div class="lg:col-span-2 space-y-6">
			{{-- Order Timeline --}}
			<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
				<div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
					<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Order Status Timeline</h4>
				</div>
				<div class="p-6">
					<x-admin.ui.timeline
						:steps="[
							['label' => 'Order Placed', 'date' => $order->created_at->format('M d, Y H:i'), 'status' => 'completed', 'description' => 'Order #' . $order->id . ' received'],
							['label' => 'Processing', 'status' => in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'completed' : ($order->status === 'pending' ? 'current' : 'failed'), 'description' => 'Order is being prepared'],
							['label' => 'Shipped', 'status' => in_array($order->status, ['shipped', 'delivered']) ? 'completed' : ($order->status === 'processing' ? 'current' : 'upcoming'), 'description' => $order->tracking_number ? 'Tracking: ' . $order->tracking_number : 'Awaiting shipment'],
							['label' => 'Delivered', 'status' => $order->status === 'delivered' ? 'completed' : ($order->status === 'shipped' ? 'current' : 'upcoming'), 'description' => $order->status === 'delivered' ? 'Order delivered successfully' : 'Pending delivery'],
						]"
					/>

					{{-- Status Update Form --}}
					<form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
						@csrf
						@method('PATCH')
						<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
							<div>
								<label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Update Status</label>
								<select id="status" name="status" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
									<option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
									<option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
									<option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
									<option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
									<option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
								</select>
							</div>
							<div>
								<label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
								<input type="text" id="notes" name="notes" value="{{ $order->status_notes }}" placeholder="Add tracking number or notes..." class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white/90 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500">
							</div>
							<div class="flex items-end">
								<button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors">
									Update Status
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>

			{{-- Order Items with Batch Traceability --}}
			<x-admin.ui.data-table
				title="Order Items ({{ $order->items->sum('quantity') }} items)"
				:columns="['Product', 'Qty', 'Price', 'Total']"
				:columnAligns="['left', 'center', 'right', 'right']"
				:hasData="$order->items->count() > 0"
				empty="No items in this order"
			>
				@foreach ($order->items as $item)
					<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
						<td class="px-5 py-3">
							<div class="flex items-center gap-3">
								@if ($item->product && $item->product->primary_image)
									<img src="{{ asset('storage/' . $item->product->primary_image) }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded-lg object-cover">
								@else
									<div class="w-12 h-12 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
										<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
										</svg>
									</div>
								@endif
								<div>
									@if($item->product)
										<a href="{{ route('admin.products.show', $item->product_id) }}" class="font-medium text-sm text-gray-800 dark:text-white/90 hover:text-brand-600 dark:hover:text-brand-400 hover:underline">
											{{ $item->product_name }}
										</a>
										@if($item->product->sku)
											<p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item->product->sku }}</p>
										@endif
									@else
										<span class="font-medium text-sm text-gray-800 dark:text-white/90">{{ $item->product_name }}</span>
									@endif
									{{-- FIFO Batch Info (if available) --}}
									@if($item->cost_price)
										<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
											COGS: ৳{{ number_format($item->cost_price, 2) }} × {{ $item->quantity }} = ৳{{ number_format($item->cost_price * $item->quantity, 2) }}
										</p>
									@endif
								</div>
							</div>
						</td>
						<td class="px-5 py-3 text-center">
							<x-admin.ui.badge color="blue">{{ $item->quantity }}</x-admin.ui.badge>
						</td>
						<td class="px-5 py-3 text-right text-sm font-medium text-gray-800 dark:text-white/90">
							৳{{ number_format($item->price, 2) }}
						</td>
						<td class="px-5 py-3 text-right text-sm font-bold text-gray-800 dark:text-white/90">
							৳{{ number_format($item->total_price, 2) }}
						</td>
					</tr>
				@endforeach

				<x-slot:footer>
					<div class="space-y-2 text-sm">
						<div class="flex items-center justify-between">
							<span class="text-gray-600 dark:text-gray-400">Subtotal</span>
							<span class="font-medium text-gray-800 dark:text-white/90">৳{{ number_format($order->total_amount, 2) }}</span>
						</div>
						@if ($order->discount_amount > 0)
							<div class="flex items-center justify-between text-green-600 dark:text-green-400">
								<span>Discount</span>
								<span class="font-medium">-৳{{ number_format($order->discount_amount, 2) }}</span>
							</div>
						@endif
						@if ($order->tax_amount > 0)
							<div class="flex items-center justify-between">
								<span class="text-gray-600 dark:text-gray-400">Tax</span>
								<span class="font-medium text-gray-800 dark:text-white/90">৳{{ number_format($order->tax_amount, 2) }}</span>
							</div>
						@endif
						<div class="flex items-center justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
							<span class="font-bold text-gray-800 dark:text-white/90">Total</span>
							<span class="font-bold text-lg text-brand-600 dark:text-brand-400">৳{{ number_format($order->total_amount - $order->discount_amount + ($order->tax_amount ?? 0), 2) }}</span>
						</div>
					</div>
				</x-slot:footer>
			</x-admin.ui.data-table>
		</div>

		{{-- Right Column - Sidebar --}}
		<div class="space-y-6">
			{{-- Customer Info --}}
			<x-admin.ui.info-card
				title="Customer Information"
				:items="array_filter([
					['label' => 'Name', 'value' => $order->customer_name],
					['label' => 'Email', 'value' => $order->customer_email],
					$order->customer_phone ? ['label' => 'Phone', 'value' => $order->customer_phone] : null,
					$order->user ? ['label' => 'Account', 'value' => $order->user->name, 'url' => route('admin.customers.show', $order->user)] : null,
				])"
			/>

			{{-- Shipping Address --}}
			@if ($order->shipping_address)
				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
					<div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Shipping Address</h4>
					</div>
					<div class="p-5">
						<address class="not-italic text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
							{{ $order->shipping_address }}
						</address>
					</div>
				</div>
			@endif

			{{-- Order Notes --}}
			@if ($order->notes)
				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
					<div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Order Notes</h4>
					</div>
					<div class="p-5">
						<p class="text-sm text-gray-600 dark:text-gray-400">{{ $order->notes }}</p>
					</div>
				</div>
			@endif

			{{-- Order Summary --}}
			<x-admin.ui.info-card
				title="Order Summary"
				:items="array_filter([
					['label' => 'Order ID', 'value' => '#' . $order->id, 'mono' => true],
					$order->order_number ? ['label' => 'Order Number', 'value' => $order->order_number, 'mono' => true] : null,
					['label' => 'Date', 'value' => $order->created_at->format('M d, Y')],
					['label' => 'Payment', 'value' => ucfirst($order->payment_method ?? 'N/A')],
					['label' => 'Items', 'value' => $order->items->sum('quantity')],
					['label' => 'Status', 'value' => ucfirst($order->status), 'badge' => match($order->status) {
						'delivered' => 'green',
						'shipped' => 'purple',
						'processing' => 'blue',
						'cancelled' => 'red',
						default => 'orange'
					}],
				])"
			/>
		</div>
	</div>
@endsection
