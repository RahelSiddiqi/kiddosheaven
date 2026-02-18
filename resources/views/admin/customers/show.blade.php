@extends('admin.layouts.app')

@section('title', $customer->name . ' — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<!-- Header with Back -->
			<div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
				<div>
					<a href="{{ route('admin.customers.index') }}"
						class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-1">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
						</svg>
						Back to Customers
					</a>
					<h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Customer Details</h1>
				</div>
			</div>

			<div class="grid gap-6 lg:grid-cols-3">
				<!-- Customer Profile Card -->
				<div class="lg:col-span-1 space-y-6">
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
						<div class="p-6 border-b border-gray-200 dark:border-gray-700">
							<div class="flex items-center gap-4">
								<div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold">
									{{ substr($customer->name, 0, 1) }}
								</div>
								<div>
									<h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h2>
									<p class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->email }}</p>
								</div>
							</div>
						</div>

						<div class="p-6 space-y-4">
							<div class="flex items-center justify-between py-2">
								<span class="text-sm text-gray-500 dark:text-gray-400">Customer ID</span>
								<span class="font-medium text-gray-900 dark:text-white">#{{ $customer->id }}</span>
							</div>
							<div class="flex items-center justify-between py-2">
								<span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
								<span class="font-medium text-gray-900 dark:text-white">{{ $customer->phone ?? 'Not provided' }}</span>
							</div>
							<div class="flex items-center justify-between py-2">
								<span class="text-sm text-gray-500 dark:text-gray-400">Joined</span>
								<span class="font-medium text-gray-900 dark:text-white">{{ $customer->created_at->format('M d, Y') }}</span>
							</div>
							<div class="flex items-center justify-between py-2">
								<span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
								@if ($customer->is_active ?? true)
									<span
										class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
										<span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
										Active
									</span>
								@else
									<span
										class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
										<span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
										Inactive
									</span>
								@endif
							</div>
						</div>

						<div class="p-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
							<!-- Quick Stats -->
							<div class="grid grid-cols-2 gap-4">
								<div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
									<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $orders->count() }}</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">Total Orders</p>
								</div>
								<div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
									<p class="text-2xl font-bold text-blue-600 dark:text-blue-400">৳
										{{ number_format($orders->sum('total_amount'), 0) }}</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">Total Spent</p>
								</div>
							</div>

							<!-- Actions -->
							<form action="{{ route('admin.customers.toggle', $customer) }}" method="POST">
								@csrf
								<button type="submit"
									class="w-full h-10.5 inline-flex items-center justify-center rounded-lg border {{ $customer->is_active ?? true ? 'border-yellow-500 text-yellow-600 hover:bg-yellow-50 dark:border-yellow-600 dark:text-yellow-400 dark:hover:bg-yellow-900/20' : 'border-blue-500 bg-blue-500 text-white hover:bg-blue-600' }} px-4 py-2.5 text-sm font-medium shadow-theme-xs">
									{{ $customer->is_active ?? true ? 'Deactivate Customer' : 'Activate Customer' }}
								</button>
							</form>
						</div>
					</div>

					<!-- Addresses Card -->
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
						<div class="p-4 border-b border-gray-200 dark:border-gray-700">
							<h3 class="font-semibold text-gray-900 dark:text-white">Saved Addresses</h3>
						</div>
						<div class="p-4">
							@if ($addresses->count() > 0)
								<div class="space-y-3">
									@foreach ($addresses as $address)
										<div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
											<div class="flex items-center gap-2 mb-2">
												<span
													class="text-xs font-medium px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded">{{ ucfirst($address->type) }}</span>
												@if ($address->is_default)
													<span class="text-xs text-gray-500 dark:text-gray-400">Default</span>
												@endif
											</div>
											<p class="text-sm text-gray-900 dark:text-white">{{ $address->address_line1 }}</p>
											@if ($address->address_line2)
												<p class="text-sm text-gray-900 dark:text-white">{{ $address->address_line2 }}</p>
											@endif
											<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $address->city }}, {{ $address->district }}
												{{ $address->postal_code }}</p>
										</div>
									@endforeach
								</div>
							@else
								<div class="text-center py-8">
									<svg class="mx-auto h-10 w-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
											d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
									</svg>
									<p class="text-sm text-gray-500 dark:text-gray-400">No saved addresses</p>
								</div>
							@endif
						</div>
					</div>
				</div>

				<!-- Orders History -->
				<div class="lg:col-span-2">
					<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
						<div
							class="flex flex-col gap-2 p-4 border-b border-gray-200 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
							<div>
								<h3 class="font-semibold text-gray-900 dark:text-white">Order History</h3>
							</div>
							<span class="text-sm text-gray-500 dark:text-gray-400">{{ $orders->count() }} orders</span>
						</div>

						@if ($orders->count() > 0)
							<div class="divide-y divide-gray-200 dark:divide-gray-700">
								@foreach ($orders as $order)
									@php
										$statusColors = [
										    'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
										    'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
										    'shipped' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
										    'delivered' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
										    'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
										];
										$statusColor =
										    $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
									@endphp
									<div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
										<div class="flex flex-col gap-2 mb-2 sm:flex-row sm:items-center sm:justify-between">
											<div class="flex items-center gap-3">
												<span class="font-semibold text-gray-900 dark:text-white">#{{ $order->id }}</span>
												<span
													class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y h:i A') }}</span>
											</div>
											<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
												{{ ucfirst($order->status) }}
											</span>
										</div>
										<div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
											<div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
												<span>{{ $order->items->count() }} items</span>
												<span>{{ $order->city ?? 'N/A' }}</span>
											</div>
											<div class="flex items-center gap-4">
												<span class="font-semibold text-gray-900 dark:text-white">৳
														{{ number_format($order->total_amount, 0) }}</span>
												<a href="{{ route('admin.orders.show', $order) }}"
													class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
													View Details
												</a>
											</div>
										</div>
									</div>
								@endforeach
							</div>
						@else
							<div class="p-8 text-center">
								<svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
										d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
								</svg>
								<p class="text-sm text-gray-500 dark:text-gray-400 mb-1">No orders yet</p>
								<p class="text-xs text-gray-400 dark:text-gray-500">This customer hasn't placed any orders</p>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
