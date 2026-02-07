@extends('admin.layouts.app')

@section('title', 'Order #' . $order->id . ' — Kiddo\'s Heaven')

@section('header_title', 'Order #' . $order->id)

@section('content')
	<div class="space-y-6">
		<!-- Header Actions -->
		<div class="flex flex-wrap items-center justify-between gap-4">
			<div class="flex items-center gap-3">
				@php
					$statusColors = [
					    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
					    'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
					    'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
					    'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
					    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
					];
				@endphp
				<span
					class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
					{{ ucfirst($order->status) }}
				</span>
				<span class="text-sm" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
					{{ $order->created_at->format('M d, Y H:i') }}
				</span>
			</div>

			<div class="flex items-center gap-2">
				<a href="{{ route('admin.orders.invoice', $order) }}" class="btn-secondary"
					:class="{ 'dark bg-slate-800 text-white hover:bg-slate-700': isDarkMode, 'bg-gray-100 text-gray-700 hover:bg-gray-200': !
					        isDarkMode }">
					<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
					</svg>
					Invoice
				</a>
				<a href="{{ route('admin.orders.index') }}" class="btn-secondary"
					:class="{ 'dark bg-slate-800 text-white hover:bg-slate-700': isDarkMode, 'bg-gray-100 text-gray-700 hover:bg-gray-200': !
					        isDarkMode }">
					<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
					</svg>
					Back to Orders
				</a>
			</div>
		</div>

		<div class="grid gap-6 md:grid-cols-3">
			<!-- Order Info -->
			<div class="md:col-span-2 space-y-6">
				<!-- Order Status Timeline -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
						<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
							Order Timeline
						</h2>
					</div>
					<div class="card-content">
						<div class="relative">
							@php
								$statuses = ['pending', 'processing', 'shipped', 'delivered'];
								$currentIndex = array_search($order->status, $statuses);
								if ($order->status === 'cancelled') {
								    $currentIndex = -1;
								}
							@endphp

							<div class="flex items-center justify-between">
								@foreach ($statuses as $index => $status)
									<div class="flex flex-col items-center">
										<div
											class="w-10 h-10 rounded-full flex items-center justify-center
										{{ $index <= $currentIndex
										    ? ($order->status === 'cancelled'
										        ? 'bg-gray-500'
										        : 'bg-blue-500')
										    : 'bg-gray-200 dark:bg-slate-700' }}">
											@switch($status)
												@case('pending')
													<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
													</svg>
												@break

												@case('processing')
													<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
													</svg>
												@break

												@case('shipped')
													<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
													</svg>
												@break

												@case('delivered')
													<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
													</svg>
												@break
											@endswitch
										</div>
										<span class="mt-2 text-xs font-medium capitalize"
											:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
											{{ $status }}
										</span>
									</div>
									@if (!$loop->last)
										<div
											class="flex-1 h-1 mx-2 rounded
										{{ $index < $currentIndex ? 'bg-blue-500' : 'bg-gray-200 dark:bg-slate-700' }}">
										</div>
									@endif
								@endforeach
							</div>
						</div>

						<!-- Status Update Form -->
						<form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mt-6 pt-6 border-t"
							:class="{ 'border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
							@csrf
							@method('PATCH')
							<div class="flex flex-wrap items-end gap-4">
								<div class="flex-1 min-w-[200px]">
									<label for="status" class="label">Update Status</label>
									<select id="status" name="status" class="input"
										:class="{ 'dark bg-slate-800 border-slate-700 text-white': isDarkMode, 'bg-white border-gray-300': !isDarkMode }">
										<option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
										<option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
										<option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
										<option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
										<option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
									</select>
								</div>
								<div class="flex-1 min-w-[200px]">
									<label for="notes" class="label">Notes (Optional)</label>
									<input type="text" id="notes" name="notes" value="{{ $order->status_notes }}"
										placeholder="Add tracking number or notes..." class="input"
										:class="{ 'dark bg-slate-800 border-slate-700 text-white': isDarkMode, 'bg-white border-gray-300': !isDarkMode }">
								</div>
								<button type="submit" class="btn-primary">
									Update Status
								</button>
							</div>
						</form>
					</div>
				</div>

				<!-- Order Items -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
						<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
							Order Items ({{ $order->items->sum('quantity') }} items)
						</h2>
					</div>
					<div class="card-content p-0">
						<div class="overflow-x-auto">
							<table class="w-full">
								<thead>
									<tr class="border-b" :class="{ 'border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
										<th class="text-left py-3 px-4 font-medium text-sm"
											:class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">Product</th>
										<th class="text-center py-3 px-4 font-medium text-sm"
											:class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">Qty</th>
										<th class="text-right py-3 px-4 font-medium text-sm"
											:class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">Price</th>
										<th class="text-right py-3 px-4 font-medium text-sm"
											:class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">Total</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($order->items as $item)
										<tr class="border-b" :class="{ 'border-slate-800': isDarkMode, 'border-gray-100': !isDarkMode }">
											<td class="py-3 px-4">
												<div class="flex items-center gap-3">
													@if ($item->product && $item->product->image)
														<img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}"
															class="w-12 h-12 rounded-lg object-cover">
													@else
														<div class="w-12 h-12 rounded-lg bg-gray-200 dark:bg-slate-700 flex items-center justify-center">
															<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																	d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
															</svg>
														</div>
													@endif
													<div>
														<a href="{{ route('admin.products.show', $item->product_id) }}"
															class="font-medium hover:underline text-sm"
															:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
															{{ $item->product_name }}
														</a>
														@if ($item->product)
															<p class="text-xs" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
																SKU: {{ $item->product->sku }}
															</p>
														@endif
													</div>
												</div>
											</td>
											<td class="py-3 px-4 text-center text-sm"
												:class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">
												{{ $item->quantity }}
											</td>
											<td class="py-3 px-4 text-right text-sm"
												:class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">
												৳{{ number_format($item->price / 100, 2) }}
											</td>
											<td class="py-3 px-4 text-right font-medium text-sm"
												:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
												৳{{ number_format(($item->quantity * $item->price) / 100, 2) }}
											</td>
										</tr>
									@endforeach
								</tbody>
								<tfoot>
									<tr class="border-b" :class="{ 'border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
										<td colspan="3" class="py-3 px-4 text-right font-medium text-sm"
											:class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">
											Subtotal
										</td>
										<td class="py-3 px-4 text-right font-medium text-sm"
											:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
											৳{{ number_format($order->total_amount / 100, 2) }}
										</td>
									</tr>
									@if ($order->discount_amount > 0)
										<tr class="border-b" :class="{ 'border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
											<td colspan="3" class="py-3 px-4 text-right font-medium text-sm"
												:class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">
												Discount
											</td>
											<td class="py-3 px-4 text-right font-medium text-sm text-green-500">
												-৳{{ number_format($order->discount_amount / 100, 2) }}
											</td>
										</tr>
									@endif
									<tr>
										<td colspan="3" class="py-3 px-4 text-right font-bold text-base"
											:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
											Total
										</td>
										<td class="py-3 px-4 text-right font-bold text-base"
											:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
											৳{{ number_format(($order->total_amount - $order->discount_amount) / 100, 2) }}
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>

			<!-- Sidebar -->
			<div class="space-y-6">
				<!-- Customer Info -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
						<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
							Customer Information
						</h2>
					</div>
					<div class="card-content space-y-4">
						<div>
							<p class="text-xs" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Name</p>
							<p class="font-medium" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
								{{ $order->customer_name }}
							</p>
						</div>
						<div>
							<p class="text-xs" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Email</p>
							<p class="font-medium" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
								{{ $order->customer_email }}
							</p>
						</div>
						<div>
							<p class="text-xs" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Phone</p>
							<p class="font-medium" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
								{{ $order->customer_phone ?? 'N/A' }}
							</p>
						</div>
						@if ($order->user)
							<div>
								<p class="text-xs" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Registered User</p>
								<a href="{{ route('admin.customers.show', $order->user) }}" class="font-medium hover:underline text-blue-500">
									{{ $order->user->name }}
								</a>
							</div>
						@endif
					</div>
				</div>

				<!-- Shipping Address -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
						<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
							Shipping Address
						</h2>
					</div>
					<div class="card-content">
						@if ($order->shipping_address)
							<address class="not-italic text-sm" :class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">
								{{ $order->shipping_address }}
							</address>
						@else
							<p class="text-sm" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
								No address provided.
							</p>
						@endif
					</div>
				</div>

				<!-- Order Notes -->
				@if ($order->notes)
					<div class="card"
						:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
						<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
							<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
								Order Notes
							</h2>
						</div>
						<div class="card-content">
							<p class="text-sm" :class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">
								{{ $order->notes }}
							</p>
						</div>
					</div>
				@endif

				<!-- Order Summary -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
						<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
							Order Summary
						</h2>
					</div>
					<div class="card-content space-y-3">
						<div class="flex justify-between text-sm">
							<span :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Order ID</span>
							<span class="font-medium"
								:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">#{{ $order->id }}</span>
						</div>
						@if ($order->order_number)
							<div class="flex justify-between text-sm">
								<span :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Order Number</span>
								<span class="font-medium"
									:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">{{ $order->order_number }}</span>
							</div>
						@endif
						<div class="flex justify-between text-sm">
							<span :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Date</span>
							<span class="font-medium"
								:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">{{ $order->created_at->format('M d, Y') }}</span>
						</div>
						<div class="flex justify-between text-sm">
							<span :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Payment Method</span>
							<span class="font-medium"
								:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
						</div>
						<div class="flex justify-between text-sm">
							<span :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Items</span>
							<span class="font-medium"
								:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">{{ $order->items->sum('quantity') }}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
