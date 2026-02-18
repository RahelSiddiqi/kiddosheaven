<!DOCTYPE html>
<html lang="en" class="light">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Invoice #{{ $order->id }} — Kiddo's Heaven</title>
		@vite(['resources/css/app.css'])

		<style>
			@media print {
				body {
					-webkit-print-color-adjust: exact;
					print-color-adjust: exact;
				}

				.no-print {
					display: none !important;
				}
			}

			body {
				font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
			}
		</style>
	</head>

	<body class="bg-gray-100 dark:bg-gray-900 min-h-screen p-8">
		<div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-lg shadow-lg overflow-hidden">
			<!-- Header -->
			<div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 p-8 text-white">
				<div class="flex items-center justify-between">
					<div>
						<h1 class="text-3xl font-bold">INVOICE</h1>
						<p class="mt-1 opacity-90">#{{ $order->id }}</p>
					</div>
					<div class="text-right">
						<img src="{{ asset('storage/logo/logo.png') }}" alt="Logo" class="h-12 w-auto mb-2 mx-auto">
						<p class="font-semibold">Kiddo's Heaven</p>
						<p class="text-sm opacity-75">Your Trusted Kids Store</p>
					</div>
				</div>
			</div>

			<!-- Invoice Info -->
			<div class="p-8 border-b dark:border-slate-700">
				<div class="grid gap-8 md:grid-cols-3">
					<div>
						<h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
							Invoice Date
						</h3>
						<p class="text-gray-900 dark:text-white font-medium">
							{{ $order->created_at->format('F d, Y') }}
						</p>
					</div>
					<div>
						<h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
							Order Status
						</h3>
						<p class="text-gray-900 dark:text-white font-medium capitalize">
							{{ $order->status }}
						</p>
					</div>
					@if ($order->order_number)
						<div>
							<h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
								Order Number
							</h3>
							<p class="text-gray-900 dark:text-white font-medium">
								{{ $order->order_number }}
							</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Customer & Shipping -->
			<div class="p-8 border-b dark:border-slate-700">
				<div class="grid gap-8 md:grid-cols-2">
					<div>
						<h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
							Billed To
						</h3>
						<div class="text-gray-900 dark:text-white">
							<p class="font-medium">{{ $order->customer_name }}</p>
							<p class="text-sm">{{ $order->customer_email }}</p>
							@if ($order->customer_phone)
								<p class="text-sm">{{ $order->customer_phone }}</p>
							@endif
						</div>
					</div>
					@if ($order->shipping_address)
						<div>
							<h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
								Ship To
							</h3>
							<address class="text-gray-900 dark:text-white not-italic text-sm">
								{{ $order->shipping_address }}
							</address>
						</div>
					@endif
				</div>
			</div>

			<!-- Order Items -->
			<div class="p-8">
				<table class="w-full">
					<thead>
						<tr class="border-b-2 dark:border-slate-700">
							<th class="text-left py-3 font-semibold text-sm text-gray-500 dark:text-gray-400 uppercase">
								Item
							</th>
							<th class="text-center py-3 font-semibold text-sm text-gray-500 dark:text-gray-400 uppercase">
								Qty
							</th>
							<th class="text-right py-3 font-semibold text-sm text-gray-500 dark:text-gray-400 uppercase">
								Unit Price
							</th>
							<th class="text-right py-3 font-semibold text-sm text-gray-500 dark:text-gray-400 uppercase">
								Total
							</th>
						</tr>
					</thead>
					<tbody class="divide-y dark:divide-slate-700">
						@foreach ($order->items as $item)
							<tr>
								<td class="py-4">
									<div class="flex items-center gap-3">
										@if ($item->product && $item->product->image)
											<img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}"
												class="w-12 h-12 rounded object-cover">
										@endif
										<div>
											<p class="font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</p>
											@if ($item->product)
												<p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item->product->sku }}</p>
											@endif
										</div>
									</div>
								</td>
								<td class="py-4 text-center text-gray-900 dark:text-white">
									{{ $item->quantity }}
								</td>
								<td class="py-4 text-right text-gray-900 dark:text-white">
									৳{{ number_format($item->price, 0) }}
								</td>
								<td class="py-4 text-right font-medium text-gray-900 dark:text-white">
									৳{{ number_format($item->quantity * $item->price, 0) }}
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<!-- Order Totals -->
			<div class="p-8 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
				<div class="flex justify-end">
					<div class="w-64 space-y-3">
						<div class="flex justify-between text-sm">
							<span class="text-gray-500 dark:text-gray-400">Subtotal</span>
							<span class="text-gray-900 dark:text-white font-medium">
								৳{{ number_format($order->total_amount, 0) }}
							</span>
						</div>
						@if ($order->discount_amount > 0)
							<div class="flex justify-between text-sm">
								<span class="text-gray-500 dark:text-gray-400">Discount</span>
								<span class="text-green-600 font-medium">
									-৳{{ number_format($order->discount_amount, 0) }}
								</span>
							</div>
						@endif
						@if ($order->shipping_cost > 0)
							<div class="flex justify-between text-sm">
								<span class="text-gray-500 dark:text-gray-400">Shipping</span>
								<span class="text-gray-900 dark:text-white font-medium">
									৳{{ number_format($order->shipping_cost, 0) }}
								</span>
							</div>
						@endif
						<div class="flex justify-between text-lg font-bold border-t dark:border-slate-700 pt-3">
							<span class="text-gray-900 dark:text-white">Total</span>
							<span class="text-gray-900 dark:text-white">
								৳{{ number_format($order->total_amount - $order->discount_amount + ($order->shipping_cost ?? 0), 0) }}
							</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Footer -->
			<div class="p-8 border-t dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50">
				<div class="text-center">
					<p class="text-sm text-gray-500 dark:text-gray-400">
						Thank you for shopping with Kiddo's Heaven!
					</p>
					<p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
						If you have any questions, please contact us at support@kiddosheaven.com
					</p>
				</div>
			</div>
		</div>

		<!-- Print Button -->
		<div class="fixed bottom-8 right-8 no-print">
			<button onclick="window.print()" class="btn-primary shadow-lg">
				<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
				</svg>
				Print Invoice
			</button>
		</div>
	</body>

</html>
