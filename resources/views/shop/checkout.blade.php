@extends('layouts.app')

@section('title', 'Checkout — Kiddo\'s Heaven')

@section('content')
	{{-- Page Header --}}
	<div class="mb-8">
		<h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
		<p class="text-gray-500 mt-1">Complete your order</p>
	</div>

	@php
		// Handle cart structure from controller
		$cartItems = isset($cart['items']) ? collect($cart['items']) : collect();
		$itemCount = $cartItems->count();
		$cartSubtotal = $cart['subtotal'] ?? 0;
		$cartTotal = $cart['total'] ?? 0;
	@endphp

	@if ($itemCount > 0)
		<div class="grid lg:grid-cols-3 gap-8">
			{{-- Checkout Form --}}
			<div class="lg:col-span-2 space-y-6">
				{{-- Contact Information --}}
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
						<span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold">1</span>
						Contact Information
					</h2>
					<div class="grid md:grid-cols-2 gap-4">
						<div class="md:col-span-2">
							<label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
							<input type="email" placeholder="your@email.com"
								class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
							<input type="tel" placeholder="+880 1XXXXXXXXX"
								class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
						</div>
					</div>
				</div>

				{{-- Shipping Address --}}
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
						<span
							class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold">2</span>
						Shipping Address
					</h2>
					<div class="grid md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
							<input type="text" placeholder="John"
								class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
							<input type="text" placeholder="Doe"
								class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
							<input type="text" placeholder="123 Main Street"
								class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-medium text-gray-700 mb-1">Apartment, Suite, etc. (optional)</label>
							<input type="text" placeholder="Apt 4B"
								class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">City</label>
							<input type="text" placeholder="Dhaka"
								class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
							<input type="text" placeholder="1200"
								class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
						</div>
					</div>
				</div>

				{{-- Payment Method --}}
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
						<span
							class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold">3</span>
						Payment Method
					</h2>
					<div class="space-y-3">
						{{-- Cash on Delivery --}}
						<label
							class="flex items-center justify-between p-4 rounded-lg border-2 border-primary bg-primary/5 cursor-pointer transition">
							<div class="flex items-center gap-3">
								<input type="radio" name="payment" value="cod" checked class="w-5 h-5 text-primary focus:ring-primary">
								<div>
									<p class="font-medium text-gray-800">Cash on Delivery</p>
									<p class="text-sm text-gray-500">Pay when you receive your order</p>
								</div>
							</div>
							<svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
						</label>

						{{-- Online Payment --}}
						<label
							class="flex items-center justify-between p-4 rounded-lg border-2 border-gray-200 cursor-pointer hover:border-primary transition">
							<div class="flex items-center gap-3">
								<input type="radio" name="payment" value="online" class="w-5 h-5 text-primary focus:ring-primary">
								<div>
									<p class="font-medium text-gray-800">Online Payment</p>
									<p class="text-sm text-gray-500">Pay with bKash, Nagad, or Card</p>
								</div>
							</div>
							<div class="flex items-center gap-2">
								<span class="text-2xl">💳</span>
							</div>
						</label>
					</div>
				</div>

				{{-- Order Notes --}}
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
					<h2 class="text-xl font-bold text-gray-900 mb-4">Order Notes (optional)</h2>
					<textarea placeholder="Special instructions for delivery..."
					 class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none resize-none"
					 rows="3"></textarea>
				</div>
			</div>

			{{-- Order Summary --}}
			<div class="lg:col-span-1">
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
					<h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>

					{{-- Cart Items --}}
					<div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
						@foreach ($cartItems as $key => $item)
							@php
								$img = $item['image_path'] ?? null;
								$itemSubtotal = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
							@endphp
							<div class="flex items-center gap-3">
								<div class="w-14 h-14 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0">
									@if ($img)
										<img src="{{ asset('storage/' . $img) }}" alt="{{ $item['name'] }}" loading="lazy"
											class="w-full h-full object-cover">
									@else
										<div class="w-full h-full flex items-center justify-center">
											<span class="text-xl">🧸</span>
										</div>
									@endif
								</div>
								<div class="flex-1 min-w-0">
									<p class="font-medium text-gray-800 text-sm truncate">{{ $item['name'] }}</p>
									<p class="text-xs text-gray-500">Qty: {{ $item['quantity'] ?? 1 }}</p>
								</div>
								<span class="font-medium text-gray-800">${{ number_format($itemSubtotal / 100, 2) }}</span>
							</div>
						@endforeach
					</div>

					{{-- Summary Details --}}
					<div class="space-y-3 mb-6 pt-4 border-t border-gray-100">
						<div class="flex justify-between text-gray-600">
							<span>Subtotal</span>
							<span>${{ number_format($cartSubtotal / 100, 2) }}</span>
						</div>
						<div class="flex justify-between text-gray-600">
							<span>Shipping</span>
							<span class="text-green-600">Free</span>
						</div>
						<div class="flex justify-between text-lg font-bold text-gray-900 pt-3 border-t border-gray-100">
							<span>Total</span>
							<span>${{ number_format($cartTotal / 100, 2) }}</span>
						</div>
					</div>

					{{-- Place Order Button --}}
					<form action="{{ route('checkout.place') }}" method="post">
						@csrf
						<button type="submit"
							class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition shadow-lg shadow-primary/30">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
							</svg>
							Place Order
						</button>
					</form>

					{{-- Trust Badges --}}
					<div class="flex items-center justify-center gap-4 mt-4 text-gray-400">
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
