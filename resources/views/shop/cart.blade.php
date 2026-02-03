@extends('layouts.app')

@section('title', 'Cart — KiddosHeaven')

@section('content')
	<div class="flex flex-col lg:flex-row gap-8">
		<div class="flex-1 bg-white rounded-xl shadow p-6 mb-8">
			<div class="flex items-center justify-between mb-4 border-b pb-3">
				<h2 class="text-xl font-bold text-[color:var(--color-primary-dark)]">Your Cart</h2>
				<div class="text-gray-500 text-sm">
					Review items before placing your Cash on Delivery order.
				</div>
			</div>
			@if (empty($cart['items']))
				<p class="text-gray-400">Your cart is empty for now. Start by adding some toys from the catalog.</p>
			@else
				<div class="overflow-x-auto">
					<table class="min-w-full text-sm">
						<thead>
							<tr class="bg-[color:var(--color-light)] text-[color:var(--color-primary-dark)]">
								<th class="py-2 px-3 font-semibold">Item</th>
								<th class="py-2 px-3 font-semibold">Price</th>
								<th class="py-2 px-3 font-semibold">Qty</th>
								<th class="py-2 px-3 font-semibold">Total</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($cart['items'] as $item)
								<tr class="border-b last:border-0">
									<td class="py-2 px-3">{{ $item['name'] }}</td>
									<td class="py-2 px-3">${{ number_format($item['price'] / 100, 2) }}</td>
									<td class="py-2 px-3">
										<form action="{{ route('cart.update', $item['product_id']) }}" method="post">
											@csrf
											<input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0"
												class="w-14 rounded border border-gray-300 px-2 py-1 text-center" onchange="this.form.submit()">
										</form>
									</td>
									<td class="py-2 px-3">${{ number_format($item['line_total'] / 100, 2) }}</td>
									<td class="py-2 px-3">
										<form action="{{ route('cart.remove', $item['product_id']) }}" method="post">
											@csrf
											<button type="submit"
												class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold text-lg hover:bg-red-200 transition"
												title="Remove">&times;</button>
										</form>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="flex items-center justify-between mt-6 p-4 rounded-lg bg-[color:var(--color-light)]">
					<div>
						<div class="text-gray-500">Subtotal</div>
						<strong
							class="text-lg text-[color:var(--color-primary-dark)]">${{ number_format($cart['subtotal'] / 100, 2) }}</strong>
					</div>
					<div class="text-right">
						<div class="text-xs text-gray-400">Payment method</div>
						<strong class="text-[color:var(--color-primary)]">Cash on Delivery</strong>
					</div>
				</div>
			@endif
		</div>
		<div class="w-full max-w-md bg-white rounded-xl shadow p-6 mb-8">
			<div class="mb-4 border-b pb-3">
				<h2 class="text-xl font-bold text-[color:var(--color-primary-dark)]">Next Step</h2>
			</div>
			<p class="text-gray-500 mb-4">
				When you are ready, continue to checkout and enter your delivery details.
				No online payment is required — you pay in cash when your order arrives.
			</p>
			<a href="{{ route('checkout.show') }}"
				class="inline-flex items-center px-6 py-3 rounded-lg bg-gradient-to-br from-[color:var(--color-primary)] to-[color:var(--color-accent)] text-white font-bold shadow hover:from-[color:var(--color-primary-dark)] transition mt-2">
				Proceed to Checkout
			</a>
		</div>
	</div>
@endsection
