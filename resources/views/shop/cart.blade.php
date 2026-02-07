@extends('layouts.app')

@section('title', 'Shopping Cart — Kiddo\'s Heaven')

@section('content')
	{{-- Page Header --}}
	<div class="mb-8">
		<h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
		<p class="text-gray-500 mt-1">Review your items before checkout</p>
	</div>

	@php
		// Handle cart structure from controller
		$cartItems = isset($cart['items']) ? collect($cart['items']) : collect();
		$itemCount = $cartItems->count();
		$cartSubtotal = $cart['subtotal'] ?? 0;
		$cartTotal = $cart['total'] ?? 0;
		$cartDiscount = $cart['discount'] ?? 0;
	@endphp

	@if ($itemCount > 0)
		<div class="flex flex-col xl:flex-row gap-8">
			{{-- Cart Items --}}
			<div class="flex-1">
				<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
					{{-- Table Header --}}
					<div class="hidden md:grid grid-cols-12 gap-4 px-6 py-4 bg-gray-50 border-b border-gray-100">
						<div class="col-span-6 text-sm font-medium text-gray-500">Product</div>
						<div class="col-span-2 text-sm font-medium text-gray-500 text-center">Price</div>
						<div class="col-span-2 text-sm font-medium text-gray-500 text-center">Quantity</div>
						<div class="col-span-2 text-sm font-medium text-gray-500 text-right">Total</div>
					</div>

					{{-- Cart Items --}}
					<div class="divide-y divide-gray-100">
						@foreach ($cartItems as $key => $item)
							@php
								$img = $item['image_path'] ?? null;
								$itemQty = $item['quantity'] ?? 1;
								$itemSubtotal = ($item['price'] ?? 0) * $itemQty;
							@endphp
							<div class="p-6">
								<div class="flex flex-col md:grid md:grid-cols-12 gap-4 items-start">
									{{-- Product Image & Info --}}
									<div class="col-span-6 flex items-start gap-4">
										<a href="{{ route('products.show', $item['slug']) }}"
											class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
											@if ($img)
												<img src="{{ asset('storage/' . $img) }}" alt="{{ $item['name'] }}" loading="lazy"
													class="w-full h-full object-cover hover:scale-105 transition duration-300">
											@else
												<div class="w-full h-full flex items-center justify-center">
													<span class="text-4xl">🧸</span>
												</div>
											@endif
										</a>
										<div class="flex-1 min-w-0">
											<a href="{{ route('products.show', $item['slug']) }}"
												class="font-semibold text-gray-800 hover:text-primary transition line-clamp-2">{{ $item['name'] }}</a>
											<p class="text-sm text-gray-500 mt-1">SKU: {{ $key }}</p>
											<div class="flex items-center gap-2 mt-3">
												{{-- Remove Button --}}
												<form action="{{ route('cart.remove', $key) }}" method="post">
													@csrf
													@method('delete')
													<button type="submit"
														class="text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-lg transition text-sm flex items-center gap-1">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
														</svg>
														Remove
													</button>
												</form>
											</div>
										</div>
									</div>

									{{-- Price (Mobile) --}}
									<div class="md:hidden flex items-center justify-between">
										<span class="text-sm text-gray-500">Price</span>
										<span class="font-medium text-gray-800">${{ number_format(($item['price'] ?? 0) / 100, 2) }}</span>
									</div>

									{{-- Price (Desktop) --}}
									<div class="hidden md:col-span-2 md:flex items-center justify-center">
										<span class="font-medium text-gray-800">${{ number_format(($item['price'] ?? 0) / 100, 2) }}</span>
									</div>

									{{-- Quantity --}}
									<div class="col-span-2 flex items-center justify-center">
										<form action="{{ route('cart.update', $key) }}" method="post" class="flex items-center">
											@csrf
											@method('patch')
											<div class="flex items-center border border-gray-200 rounded-lg">
												<button type="submit" name="quantity" value="{{ max(1, $itemQty - 1) }}"
													class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 text-gray-600 transition rounded-l-lg">-</button>
												<input type="number" value="{{ $itemQty }}" readonly
													class="w-12 h-10 text-center border-x border-gray-200 focus:outline-none">
												<button type="submit" name="quantity" value="{{ $itemQty + 1 }}"
													class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 text-gray-600 transition rounded-r-lg">+</button>
											</div>
										</form>
									</div>

									{{-- Subtotal --}}
									<div class="col-span-2 flex items-center justify-between md:justify-end gap-4">
										<span class="md:hidden text-sm text-gray-500">Total</span>
										<span class="text-lg font-bold text-primary-dark">${{ number_format($itemSubtotal / 100, 2) }}</span>
									</div>
								</div>
							</div>
						@endforeach
					</div>

					{{-- Continue Shopping --}}
					<div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
						<a href="{{ route('catalog') }}"
							class="inline-flex items-center gap-2 text-primary font-medium hover:text-primary-dark transition">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
							</svg>
							Continue Shopping
						</a>
					</div>
				</div>
			</div>

			{{-- Order Summary --}}
			<div class="xl:w-96 flex-shrink-0">
				<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
					<h2 class="text-xl font-bold text-gray-900 mb-6">Order Summary</h2>

					{{-- Promo Code --}}
					<div class="mb-6">
						<label class="block text-sm font-medium text-gray-700 mb-2">Have a promo code?</label>
						<div class="flex gap-2">
							<input type="text" placeholder="Enter code"
								class="flex-1 rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
							<button type="button"
								class="px-4 py-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
								Apply
							</button>
						</div>
					</div>

					{{-- Summary Details --}}
					<div class="space-y-3 mb-6">
						<div class="flex justify-between text-gray-600">
							<span>Subtotal ({{ $itemCount }} items)</span>
							<span>${{ number_format($cartSubtotal / 100, 2) }}</span>
						</div>
						<div class="flex justify-between text-gray-600">
							<span>Shipping</span>
							<span class="text-green-600">Free</span>
						</div>
						@if ($cartDiscount > 0)
							<div class="flex justify-between text-green-600">
								<span>Discount</span>
								<span>-${{ number_format($cartDiscount / 100, 2) }}</span>
							</div>
						@endif
						<div class="border-t border-gray-100 pt-3 flex justify-between text-xl font-bold text-gray-900">
							<span>Total</span>
							<span>${{ number_format($cartTotal / 100, 2) }}</span>
						</div>
					</div>

					{{-- Checkout Button --}}
					<a href="{{ route('checkout.show') }}"
						class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition shadow-lg shadow-primary/30 mb-4">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
						</svg>
						Proceed to Checkout
					</a>

					{{-- Trust Badges --}}
					<div class="flex items-center justify-center gap-4 text-gray-400">
						<div class="flex items-center gap-1 text-xs">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
							</svg>
							Secure
						</div>
						<div class="flex items-center gap-1 text-xs">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
							COD Available
						</div>
					</div>

					{{-- Delivery Info --}}
					<div class="mt-6 p-4 bg-light rounded-xl">
						<div class="flex items-start gap-3">
							<svg class="w-5 h-5 text-primary mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
							</svg>
							<div class="text-sm text-gray-600">
								<p class="font-medium text-gray-800">Free delivery on orders over $50</p>
								<p class="mt-1">Estimated delivery: 2-4 business days</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	@else
		{{-- Empty Cart --}}
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
			<span class="text-6xl mb-4 block">🛒</span>
			<h2 class="text-2xl font-bold text-gray-800 mb-2">Your cart is empty</h2>
			<p class="text-gray-500 mb-8 max-w-md mx-auto">Looks like you haven't added any toys to your cart yet. Start shopping
				to find the perfect
				gift for your little one!</p>
			<a href="{{ route('catalog') }}"
				class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition shadow-lg shadow-primary/30">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
				</svg>
				Start Shopping
			</a>

			{{-- Featured Categories --}}
			<div class="mt-12">
				<h3 class="font-bold text-gray-800 mb-6">Popular Categories</h3>
				<div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
					<a href="{{ route('catalog', ['catalog_id' => 1]) }}"
						class="p-4 rounded-xl bg-light hover:bg-primary/10 transition">
						<span class="text-3xl block mb-2">🧸</span>
						<span class="font-medium text-gray-700">Stuffed Toys</span>
					</a>
					<a href="{{ route('catalog', ['catalog_id' => 2]) }}"
						class="p-4 rounded-xl bg-light hover:bg-primary/10 transition">
						<span class="text-3xl block mb-2">🚗</span>
						<span class="font-medium text-gray-700">Vehicles</span>
					</a>
					<a href="{{ route('catalog', ['catalog_id' => 3]) }}"
						class="p-4 rounded-xl bg-light hover:bg-primary/10 transition">
						<span class="text-3xl block mb-2">🧩</span>
						<span class="font-medium text-gray-700">Puzzles</span>
					</a>
					<a href="{{ route('catalog') }}" class="p-4 rounded-xl bg-light hover:bg-primary/10 transition">
						<span class="text-3xl block mb-2">🎨</span>
						<span class="font-medium text-gray-700">Art & Craft</span>
					</a>
				</div>
			</div>
		</div>
	@endif
@endsection
