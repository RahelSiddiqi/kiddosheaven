@extends('layouts.app')

@section('title', 'My Orders — Kiddo\'s Heaven')

@section('content')
	{{-- Breadcrumb --}}
	<nav class="text-xs sm:text-sm mb-4 sm:mb-6">
		<ol class="flex items-center gap-1.5 sm:gap-2 text-gray-500">
			<li><a href="{{ route('home') }}" class="hover:text-primary">Home</a></li>
			<li><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
			<li><a href="{{ route('account') }}" class="hover:text-primary">Account</a></li>
			<li><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
			<li class="text-gray-800 font-medium">Orders</li>
		</ol>
	</nav>

	<h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-4 sm:mb-6">My Orders</h1>

	{{-- Status Filter --}}
	<div class="flex gap-2 overflow-x-auto mb-6 sm:mb-8 pb-2">
		<a href="{{ route('customer.orders.index') }}"
			class="px-3 sm:px-4 py-2 rounded-lg border text-xs sm:text-sm font-medium whitespace-nowrap transition {{ !request('status') ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
			All
		</a>
		@foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
			<a href="{{ route('customer.orders.index', ['status' => $s]) }}"
				class="px-3 sm:px-4 py-2 rounded-lg border text-xs sm:text-sm font-medium whitespace-nowrap transition {{ request('status') === $s ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
				{{ ucfirst($s) }}
			</a>
		@endforeach
	</div>

	{{-- Orders List --}}
	@if ($orders->isEmpty())
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center">
			<svg class="w-12 sm:w-16 h-12 sm:h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
			</svg>
			<h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">No orders found</h3>
			<p class="text-gray-500 mb-6 text-sm sm:text-base">{{ request('status') ? 'No ' . request('status') . ' orders.' : 'You haven\'t placed any orders yet.' }}</p>
			<a href="{{ route('catalog') }}"
				class="inline-flex items-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition text-sm sm:text-base">
				Start Shopping
			</a>
		</div>
	@else
		<div class="space-y-3 sm:space-y-4">
			@foreach ($orders as $order)
				<a href="{{ route('customer.orders.show', $order->id) }}"
					class="block bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 hover:border-primary/30 hover:shadow-md transition">
					<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
						<div>
							<div class="flex items-center gap-2 sm:gap-3 mb-2">
								<span class="font-bold text-gray-900 text-base sm:text-lg">#{{ $order->order_number ?? $order->id }}</span>
								<span @class([
									'px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold',
									'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
									'bg-blue-100 text-blue-700' => $order->status === 'processing',
									'bg-green-100 text-green-700' => $order->status === 'delivered',
									'bg-red-100 text-red-700' => $order->status === 'cancelled',
									'bg-purple-100 text-purple-700' => $order->status === 'shipped',
								])>{{ ucfirst($order->status) }}</span>
							</div>
							<p class="text-xs sm:text-sm text-gray-500">
								{{ $order->created_at->format('M d, Y \a\t h:i A') }}
								<span class="hidden sm:inline">·</span>
								<span class="block sm:inline mt-0.5 sm:mt-0">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
							</p>
						</div>
						<div class="text-left sm:text-right">
							<p class="text-xl sm:text-2xl font-bold text-primary-dark">৳{{ number_format($order->total_amount, 0) }}</p>
							<p class="text-xs sm:text-sm text-gray-500">{{ ucfirst($order->payment_method ?? 'N/A') }}</p>
						</div>
					</div>
				</a>
			@endforeach
		</div>

		<div class="mt-6 sm:mt-8">
			{{ $orders->appends(request()->query())->links() }}
		</div>
	@endif
@endsection
