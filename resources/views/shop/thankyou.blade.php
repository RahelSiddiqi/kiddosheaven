@extends('layouts.app')

@section('title', 'Thank You — Kiddo\'s Heaven')

@section('content')
	<div class="min-h-[70vh] flex items-center justify-center">
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center max-w-lg">
			{{-- Success Icon --}}
			<div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
				<svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
				</svg>
			</div>

			{{-- Success Message --}}
			<h1 class="text-3xl font-bold text-gray-900 mb-4">Thank You for Your Order!</h1>
			<p class="text-gray-600 mb-2">Your order has been placed successfully.</p>
			<p class="text-gray-500 mb-8">Order #{{ $order->id ?? 'KH-' . time() }} • We've sent a confirmation to your email.</p>

			{{-- Order Details --}}
			<div class="bg-light rounded-xl p-6 mb-8 text-left">
				<h3 class="font-bold text-gray-800 mb-4">Order Summary</h3>
				@if (isset($order))
					<div class="space-y-2 text-sm">
						<div class="flex justify-between">
							<span class="text-gray-500">Date</span>
							<span class="text-gray-800">{{ $order->created_at->format('M d, Y') }}</span>
						</div>
						<div class="flex justify-between">
							<span class="text-gray-500">Payment Method</span>
							<span class="text-gray-800">{{ $order->payment_method ?? 'Cash on Delivery' }}</span>
						</div>
						<div class="flex justify-between">
							<span class="text-gray-500">Total</span>
							<span class="text-lg font-bold text-primary-dark">${{ number_format(($order->total ?? 0) / 100, 2) }}</span>
						</div>
					</div>
				@else
					<p class="text-gray-500 text-sm">Thank you for shopping with Kiddo's Heaven!</p>
				@endif
			</div>

			{{-- What Happens Next --}}
			<div class="mb-8">
				<h3 class="font-bold text-gray-800 mb-4">What's Next?</h3>
				<div class="grid grid-cols-3 gap-4 text-center">
					<div>
						<div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-2">
							<svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
							</svg>
						</div>
						<p class="text-xs text-gray-600">Confirmation<br>Email Sent</p>
					</div>
					<div>
						<div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-2">
							<svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
							</svg>
						</div>
						<p class="text-xs text-gray-600">Order<br>Processing</p>
					</div>
					<div>
						<div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-2">
							<svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
							</svg>
						</div>
						<p class="text-xs text-gray-600">Delivered<br>to You</p>
					</div>
				</div>
			</div>

			{{-- Action Buttons --}}
			<div class="flex flex-col sm:flex-row gap-4">
				<a href="{{ route('account') }}"
					class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-xl border-2 border-primary text-primary font-bold hover:bg-primary hover:text-white transition">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
					</svg>
					View Orders
				</a>
				<a href="{{ route('catalog') }}"
					class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/30">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
					</svg>
					Continue Shopping
				</a>
			</div>
		</div>
	</div>
@endsection
