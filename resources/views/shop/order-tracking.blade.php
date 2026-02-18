@extends('layouts.app')

@section('title', 'Track Order — Kiddo\'s Heaven')

@section('content')
	{{-- Page Header --}}
	<div class="mb-6 sm:mb-8">
		<h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Track Your Order</h1>
		<p class="text-gray-500 mt-1 text-sm sm:text-base">Enter your order ID to track the delivery status</p>
	</div>

	<div class="max-w-2xl mx-auto">
		{{-- Search Form --}}
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 md:p-8 mb-6 sm:mb-8">
			<form action="{{ route('track.order') }}" method="get" class="space-y-4">
				<div>
					<label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Order ID</label>
					<div class="flex flex-col sm:flex-row gap-3">
						<input type="text" name="order_id" value="{{ request('order_id') }}" placeholder="Enter your order number (e.g., ORD-12345)"
							class="flex-1 rounded-lg border border-gray-200 px-4 py-3 text-sm sm:text-base focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none"
							required>
						<button type="submit"
							class="px-6 sm:px-8 py-3 rounded-lg bg-primary text-white font-bold text-sm sm:text-base hover:bg-primary-dark transition shadow-lg shadow-primary/30 whitespace-nowrap">
							Track Order
						</button>
					</div>
					<p class="text-xs sm:text-sm text-gray-500 mt-2">You can find your order ID in your order confirmation email or my orders page.</p>
				</div>
			</form>
		</div>

		{{-- Order Details --}}
		@if($order)
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 md:p-8">
				{{-- Order Header --}}
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
					<div>
						<div class="flex items-center gap-3 mb-2">
							<h2 class="text-lg sm:text-xl font-bold text-gray-900">Order #{{ $order->order_number ?? $order->id }}</h2>
							<span @class([
								'px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-bold',
								'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
								'bg-blue-100 text-blue-700' => $order->status === 'processing',
								'bg-purple-100 text-purple-700' => $order->status === 'shipped',
								'bg-green-100 text-green-700' => $order->status === 'delivered',
								'bg-red-100 text-red-700' => $order->status === 'cancelled',
							])>
								{{ ucfirst($order->status) }}
							</span>
						</div>
						<p class="text-xs sm:text-sm text-gray-500">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
					</div>
					<div class="text-left sm:text-right">
						<p class="text-xl sm:text-2xl font-bold text-primary-dark">৳{{ number_format($order->total_amount, 0) }}</p>
						<p class="text-xs sm:text-sm text-gray-500">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</p>
					</div>
				</div>

				{{-- Order Timeline --}}
				<div class="mb-6 sm:mb-8">
					<h3 class="font-bold text-gray-900 mb-4 text-sm sm:text-base">Order Progress</h3>
					<div class="relative">
						{{-- Timeline Line --}}
						<div class="absolute left-4 sm:left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
						
						@php
							$steps = [
								['status' => 'pending', 'label' => 'Order Placed', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
								['status' => 'processing', 'label' => 'Processing', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
								['status' => 'shipped', 'label' => 'Shipped', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
								['status' => 'delivered', 'label' => 'Delivered', 'icon' => 'M5 13l4 4L19 7'],
							];
							
							$currentStatusIndex = array_search($order->status, array_column($steps, 'status'));
							if($currentStatusIndex === false) $currentStatusIndex = -1;
						@endphp

						<div class="space-y-4 sm:space-y-6">
							@foreach($steps as $index => $step)
								@php
									$isCompleted = $index <= $currentStatusIndex;
									$isCurrent = $index === $currentStatusIndex;
								@endphp
								<div class="relative flex items-start gap-3 sm:gap-4">
									<div @class([
										'relative z-10 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center flex-shrink-0 transition-all',
										'bg-primary text-white' => $isCompleted,
										'bg-gray-100 text-gray-400' => !$isCompleted,
									])>
										<svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}" />
										</svg>
										@if($isCurrent)
											<div class="absolute -inset-1 rounded-full border-2 border-primary animate-pulse"></div>
										@endif
									</div>
									<div class="flex-1 pt-1.5 sm:pt-2">
										<p @class([
											'font-medium text-sm sm:text-base',
											'text-primary' => $isCurrent,
											'text-gray-900' => $isCompleted && !$isCurrent,
											'text-gray-400' => !$isCompleted,
										])>
											{{ $step['label'] }}
										</p>
										@if($isCurrent && isset($order->statusHistory) && $order->statusHistory->isNotEmpty())
											@php $latestHistory = $order->statusHistory->first(); @endphp
											@if($latestHistory)
												<p class="text-xs sm:text-sm text-gray-500 mt-0.5">
													{{ $latestHistory->created_at->format('M d, Y - h:i A') }}
												</p>
											@endif
										@endif
									</div>
								</div>
							@endforeach
						</div>
					</div>
				</div>

				{{-- Order Items --}}
				<div>
					<h3 class="font-bold text-gray-900 mb-4 text-sm sm:text-base">Order Items</h3>
					<div class="space-y-3">
						@foreach($order->items as $item)
							<div class="flex items-center gap-3 sm:gap-4">
								<div class="w-12 h-12 sm:w-16 sm:h-16 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0">
									@if($item->product && $item->product->primary_image)
										<img src="{{ asset('storage/' . $item->product->primary_image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
									@else
										<div class="w-full h-full flex items-center justify-center">
											<span class="text-xl sm:text-2xl">🧸</span>
										</div>
									@endif
								</div>
								<div class="flex-1 min-w-0">
									<p class="font-medium text-gray-800 text-sm sm:text-base truncate">{{ $item->product_name }}</p>
									<p class="text-xs sm:text-sm text-gray-500">Qty: {{ $item->quantity }} × ৳{{ number_format($item->unit_price, 0) }}</p>
								</div>
								<span class="font-bold text-gray-800 text-sm sm:text-base">৳{{ number_format($item->quantity * $item->unit_price, 0) }}</span>
							</div>
						@endforeach
					</div>
				</div>

				{{-- Delivery Address --}}
				@if($order->shipping_address)
					<div class="mt-6 pt-6 border-t border-gray-100">
						<h3 class="font-bold text-gray-900 mb-3 text-sm sm:text-base">Delivery Address</h3>
						<p class="text-sm text-gray-600">{{ $order->shipping_address }}</p>
					</div>
				@endif
			</div>
		@elseif(request('order_id'))
			{{-- Not Found --}}
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center">
				<span class="text-5xl sm:text-6xl mb-4 block">😕</span>
				<h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Order Not Found</h3>
				<p class="text-gray-500 mb-6 sm:mb-8">We couldn't find an order with that ID. Please check your order ID and try again.</p>
				<a href="{{ route('account') }}"
					class="inline-flex items-center gap-2 px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-primary text-white font-bold text-sm sm:text-base hover:bg-primary-dark transition">
					View My Orders
				</a>
			</div>
		@else
			{{-- Help Section --}}
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 md:p-8">
				<h3 class="font-bold text-gray-900 mb-4 text-base sm:text-lg">How to track your order?</h3>
				<div class="space-y-4">
					<div class="flex items-start gap-3 sm:gap-4">
						<div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
							<span class="text-primary font-bold text-sm sm:text-base">1</span>
						</div>
						<div>
							<p class="font-medium text-gray-800 text-sm sm:text-base">Find your order ID</p>
							<p class="text-xs sm:text-sm text-gray-500 mt-1">Check your order confirmation email or visit the orders page in your account.</p>
						</div>
					</div>
					<div class="flex items-start gap-3 sm:gap-4">
						<div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
							<span class="text-primary font-bold text-sm sm:text-base">2</span>
						</div>
						<div>
							<p class="font-medium text-gray-800 text-sm sm:text-base">Enter order ID</p>
							<p class="text-xs sm:text-sm text-gray-500 mt-1">Type your order ID in the search box above and click Track Order.</p>
						</div>
					</div>
					<div class="flex items-start gap-3 sm:gap-4">
						<div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
							<span class="text-primary font-bold text-sm sm:text-base">3</span>
						</div>
						<div>
							<p class="font-medium text-gray-800 text-sm sm:text-base">View delivery status</p>
							<p class="text-xs sm:text-sm text-gray-500 mt-1">See the current status of your order and estimated delivery time.</p>
						</div>
					</div>
				</div>

				<div class="mt-6 pt-6 border-t border-gray-100">
					<p class="text-sm text-gray-500">Need help? <a href="{{ route('contact') }}" class="text-primary font-medium hover:text-primary-dark">Contact us</a></p>
				</div>
			</div>
		@endif
	</div>
@endsection
