@extends('layouts.app')

@section('title', 'My Account — Kiddo\'s Heaven')

@section('content')
	{{-- Page Header --}}
	<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
		<div>
			<h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">My Account</h1>
			<p class="text-gray-500 mt-1 text-sm sm:text-base">Welcome back, {{ $user->name }}</p>
		</div>
		<form action="{{ route('logout') }}" method="POST">
			@csrf
			<button type="submit"
				class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition text-sm font-medium w-full sm:w-auto justify-center">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
				</svg>
				Logout
			</button>
		</form>
	</div>

	<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
		{{-- Profile Section --}}
		<div class="lg:col-span-1">
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
				<div class="text-center mb-4 sm:mb-6">
					<div class="w-16 sm:w-20 h-16 sm:h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-3">
						<span class="text-2xl sm:text-3xl font-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
					</div>
					<h2 class="font-bold text-gray-900 text-base sm:text-lg">{{ $user->name }}</h2>
					<p class="text-xs sm:text-sm text-gray-500">{{ $user->email }}</p>
				</div>

				<form action="{{ route('account.update') }}" method="POST" class="space-y-3 sm:space-y-4">
					@csrf
					@method('PUT')
					<div>
						<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Full Name</label>
						<input type="text" name="name" value="{{ old('name', $user->name) }}"
							class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm sm:text-base">
						@error('name') <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p> @enderror
					</div>
					<div>
						<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Phone</label>
						<input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
							class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm sm:text-base">
						@error('phone') <p class="text-red-600 text-xs sm:text-sm mt-1">{{ $message }}</p> @enderror
					</div>
					<button type="submit"
						class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg bg-primary text-white font-bold text-sm sm:text-base hover:bg-primary-dark transition">
						Update Profile
					</button>
				</form>
			</div>

			{{-- Quick Links --}}
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mt-6">
				<h3 class="font-bold text-gray-900 mb-3 sm:mb-4 text-sm sm:text-base">Quick Links</h3>
				<div class="space-y-1 sm:space-y-2">
					<a href="{{ route('customer.orders.index') }}"
						class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-lg hover:bg-gray-50 transition text-sm">
						<svg class="w-4 sm:w-5 h-4 sm:h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
						</svg>
						<span class="font-medium text-gray-700">All Orders</span>
					</a>
					<a href="{{ route('account.addresses') }}"
						class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-lg hover:bg-gray-50 transition text-sm">
						<svg class="w-4 sm:w-5 h-4 sm:h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
						</svg>
						<span class="font-medium text-gray-700">My Addresses</span>
					</a>
					<a href="{{ route('wishlist.index') }}"
						class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-lg hover:bg-gray-50 transition text-sm">
						<svg class="w-4 sm:w-5 h-4 sm:h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
						</svg>
						<span class="font-medium text-gray-700">My Wishlist</span>
					</a>
					<a href="{{ route('catalog') }}"
						class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-lg hover:bg-gray-50 transition text-sm">
						<svg class="w-4 sm:w-5 h-4 sm:h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
						</svg>
						<span class="font-medium text-gray-700">Continue Shopping</span>
					</a>
					<a href="{{ route('track.order') }}"
						class="flex items-center gap-2 sm:gap-3 p-2.5 sm:p-3 rounded-lg hover:bg-gray-50 transition text-sm">
						<svg class="w-4 sm:w-5 h-4 sm:h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
						</svg>
						<span class="font-medium text-gray-700">Track Order</span>
					</a>
				</div>
			</div>
		</div>

		{{-- Recent Orders --}}
		<div class="lg:col-span-2">
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 sm:mb-6">
					<h2 class="text-lg sm:text-xl font-bold text-gray-900">Recent Orders</h2>
					<a href="{{ route('customer.orders.index') }}" class="text-primary hover:text-primary-dark font-medium text-sm">View All</a>
				</div>

				@if ($orders->isEmpty())
					<div class="text-center py-8">
						<svg class="w-10 sm:w-12 h-10 sm:h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
						</svg>
						<p class="text-gray-500 mb-4 text-sm sm:text-base">No orders yet</p>
						<a href="{{ route('catalog') }}"
							class="inline-flex items-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition text-sm">
							Start Shopping
						</a>
					</div>
				@else
					<div class="space-y-3 sm:space-y-4">
						@foreach ($orders as $order)
							<a href="{{ route('customer.orders.show', $order->id) }}"
								class="block p-3 sm:p-4 rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-sm transition">
								<div class="flex items-center justify-between mb-2">
									<span class="font-bold text-gray-900 text-sm sm:text-base">#{{ $order->order_number ?? $order->id }}</span>
									<span @class([
										'px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold',
										'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
										'bg-blue-100 text-blue-700' => $order->status === 'processing',
										'bg-green-100 text-green-700' => $order->status === 'delivered',
										'bg-red-100 text-red-700' => $order->status === 'cancelled',
										'bg-purple-100 text-purple-700' => $order->status === 'shipped',
									])>{{ ucfirst($order->status) }}</span>
								</div>
								<div class="flex items-center justify-between text-xs sm:text-sm text-gray-500">
									<span>{{ $order->created_at->format('M d, Y') }}</span>
									<span class="font-bold text-primary-dark">৳{{ number_format($order->total_amount, 0) }}</span>
								</div>
							</a>
						@endforeach
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
