@extends('layouts.app')

@section('title', 'Checkout — Kiddo\'s Heaven')

@section('content')
	{{-- Page Header --}}
	<div class="mb-4 sm:mb-6 md:mb-8">
		<h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Checkout</h1>
		<p class="text-gray-500 mt-1 text-sm">Complete your order</p>
	</div>

	@php
		$cartItems = isset($cart['items']) ? collect($cart['items']) : collect();
		$itemCount = $cartItems->count();
		$cartSubtotal = $cart['subtotal'] ?? 0;
	@endphp

	@if ($itemCount > 0)
		<form action="{{ route('checkout.place') }}" method="post">
			@csrf
			<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
				{{-- Checkout Form --}}
				<div class="sm:col-span-2 space-y-3 sm:space-y-4 md:space-y-6">
					{{-- Contact Information --}}
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
						<h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
							<span class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs sm:text-sm font-bold">1</span>
							Contact Information
						</h2>
						<div class="grid sm:grid-cols-2 gap-3 sm:gap-4">
							<div class="sm:col-span-2">
								<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Full Name *</label>
								<input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" placeholder="John Doe" required autocomplete="name"
									class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm sm:text-base">
								@error('customer_name') <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p> @enderror
							</div>
							<div>
								<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email Address</label>
								<input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" placeholder="your@email.com" autocomplete="email"
									class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm sm:text-base">
								@error('customer_email') <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p> @enderror
							</div>
							<div>
								<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
								<input type="tel" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" placeholder="+880 1XXXXXXXXX" required autocomplete="tel"
									class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm sm:text-base">
								@error('customer_phone') <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p> @enderror
							</div>
						</div>
					</div>

					{{-- Shipping Address --}}
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
						<h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
							<span class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs sm:text-sm font-bold">2</span>
							Shipping Address
						</h2>
						<div class="grid sm:grid-cols-2 gap-3 sm:gap-4">
							<div class="sm:col-span-2">
								<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Street Address *</label>
								<input type="text" name="address_line" value="{{ old('address_line') }}" placeholder="123 Main Street, Apt 4B" required autocomplete="street-address"
									class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm sm:text-base">
								@error('address_line') <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p> @enderror
							</div>
							<div>
								<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">City *</label>
								<input type="text" name="city" value="{{ old('city') }}" placeholder="Dhaka" required autocomplete="address-level2"
									class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm sm:text-base">
								@error('city') <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p> @enderror
							</div>
							<div>
								<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Postal Code</label>
								<input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="1200" autocomplete="postal-code"
									class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm sm:text-base">
								@error('postal_code') <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p> @enderror
							</div>
						</div>
					</div>

					{{-- Payment Method --}}
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
						<h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
							<span class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs sm:text-sm font-bold">3</span>
							Payment Method
						</h2>
						<div class="space-y-2 sm:space-y-3">
							<label class="flex items-center justify-between p-3 sm:p-4 rounded-lg border-2 border-primary bg-primary/5 cursor-pointer transition">
								<div class="flex items-center gap-2 sm:gap-3">
									<input type="radio" name="payment" value="cod" checked class="w-4 h-4 sm:w-5 sm:h-5 text-primary focus:ring-primary">
									<div>
										<p class="font-medium text-gray-800 text-sm sm:text-base">Cash on Delivery</p>
										<p class="text-xs sm:text-sm text-gray-500">Pay when you receive</p>
									</div>
								</div>
								<svg class="w-6 h-6 sm:w-8 sm:h-8 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
								</svg>
							</label>
						</div>
					</div>

					{{-- Order Notes --}}
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
						<h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 mb-3 sm:mb-4">Order Notes (optional)</h2>
						<textarea name="notes" placeholder="Special instructions for delivery..."
							class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none resize-none text-sm sm:text-base"
							rows="3">{{ old('notes') }}</textarea>
					</div>

					{{-- Coupon Code --}}
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
						<h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 mb-3 sm:mb-4">Have a coupon?</h2>
						<div class="flex gap-2">
							<input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Enter coupon code"
								class="flex-1 rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm sm:text-base">
						</div>
					</div>
				</div>

				{{-- Order Summary --}}
				<div class="sm:col-span-2 lg:col-span-1 order-first sm:order-last">
					<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 sticky top-24">
						<h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Order Summary</h2>

						{{-- Cart Items --}}
						<div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6 max-h-48 sm:max-h-64 overflow-y-auto">
							@foreach ($cartItems as $key => $item)
								@php
									$img = $item['image'] ?? $item['image_path'] ?? null;
									$itemSubtotal = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
								@endphp
								<div class="flex items-center gap-2 sm:gap-3">
									<div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0">
										@if ($img)
											<img src="{{ asset('storage/' . $img) }}" alt="{{ $item['name'] }}" loading="lazy"
												class="w-full h-full object-cover">
										@else
											<div class="w-full h-full flex items-center justify-center">
												<span class="text-lg sm:text-xl">🧸</span>
											</div>
										@endif
									</div>
									<div class="flex-1 min-w-0">
										<p class="font-medium text-gray-800 text-xs sm:text-sm truncate">{{ $item['name'] }}</p>
										@if (!empty($item['variant_attributes']))
											<p class="text-[10px] text-gray-400 truncate">
												{{ collect($item['variant_attributes'])->map(fn($v, $k) => "$k: $v")->implode(', ') }}
											</p>
										@endif
										<p class="text-xs text-gray-500">Qty: {{ $item['quantity'] ?? 1 }}</p>
									</div>
									<span class="font-medium text-gray-800 text-xs sm:text-sm">৳{{ number_format($itemSubtotal, 0) }}</span>
								</div>
							@endforeach
						</div>

						{{-- Summary Details --}}
						<div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6 pt-3 sm:pt-4 border-t border-gray-100">
							<div class="flex justify-between text-sm text-gray-600">
								<span>Subtotal</span>
								<span>৳{{ number_format($cartSubtotal, 0) }}</span>
							</div>
							<div class="flex justify-between text-sm text-gray-600">
								<span>Shipping</span>
								<span class="text-green-600">Free</span>
							</div>
							<div class="flex justify-between text-base sm:text-lg font-bold text-gray-900 pt-2 sm:pt-3 border-t border-gray-100">
								<span>Total</span>
								<span>৳{{ number_format($cartSubtotal, 0) }}</span>
							</div>
						</div>

						{{-- Place Order Button --}}
						<button type="submit"
							class="w-full flex items-center justify-center gap-2 px-4 sm:px-6 py-3 sm:py-4 rounded-xl bg-primary text-white font-bold text-sm sm:text-base hover:bg-primary-dark transition shadow-lg shadow-primary/30">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
							</svg>
							Place Order
						</button>

						{{-- Trust Badges --}}
						<div class="flex items-center justify-center gap-3 sm:gap-4 mt-3 sm:mt-4 text-gray-400">
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
										d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
								</svg>
								Easy Returns
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	@else
		{{-- Empty Cart --}}
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
			<span class="text-6xl mb-4 block">🛒</span>
			<h2 class="text-2xl font-bold text-gray-800 mb-2">Your cart is empty</h2>
			<p class="text-gray-500 mb-8">Add some toys to your cart before checking out.</p>
			<a href="{{ route('catalog') }}"
				class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition shadow-lg shadow-primary/30">
				Browse Products
			</a>
		</div>
	@endif
@endsection
