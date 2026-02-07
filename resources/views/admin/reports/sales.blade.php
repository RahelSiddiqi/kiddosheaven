@extends('admin.layouts.app')

@section('title', 'Sales Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales</p>
					<p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">৳{{ number_format($totalSales, 2) }}</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Orders</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalOrders) }}</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg Order Value</p>
					<p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">৳{{ number_format($avgOrderValue, 2) }}</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Period</p>
					<p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">{{ date('M d, Y', strtotime($fromDate)) }} -
						{{ date('M d, Y', strtotime($toDate)) }}</p>
				</div>
			</div>

			<!-- Filter Form -->
			<form method="GET"
				class="rounded-2xl border border-gray-200 bg-white p-4 mb-6 dark:border-gray-800 dark:bg-white/3">
				<div class="flex flex-wrap items-end gap-4">
					<div class="w-40">
						<label for="from_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">From Date</label>
						<input type="date" name="from_date" id="from_date" value="{{ $fromDate }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
					<div class="w-40">
						<label for="to_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">To Date</label>
						<input type="date" name="to_date" id="to_date" value="{{ $toDate }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
					</div>
					<div class="w-40">
						<label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Order
							Status</label>
						<select name="status" id="status"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">All Status</option>
							<option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
							<option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
							<option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
							<option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
							<option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
						</select>
					</div>
					<div class="w-40">
						<label for="payment_status"
							class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Payment</label>
						<select name="payment_status" id="payment_status"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">All Payments</option>
							<option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
							<option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
							<option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
						</select>
					</div>
					<button type="submit"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
						Filter
					</button>
					<a href="{{ route('admin.reports.sales') }}"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
						Reset
					</a>
				</div>
			</form>

			@if ($orders->isEmpty())
				<div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/3">
					<svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
							d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
					</svg>
					<p class="text-sm font-medium text-gray-900 dark:text-white">No orders found</p>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No orders found for the selected filters.</p>
				</div>
			@else
				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Sales Report</h3>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Order details for the selected period</p>
						</div>
						<a href="{{ route('admin.orders.export') }}?from_date={{ $fromDate }}&to_date={{ $toDate }}"
							class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
							</svg>
							Export
						</a>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Order ID</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Customer</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Date</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Amount</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Status</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Payment</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($orders as $order)
										<tr>
											<td class="py-4 whitespace-nowrap">
												<a href="{{ route('admin.orders.show', $order) }}"
													class="text-sm font-semibold text-blue-600 hover:underline">
													#{{ $order->order_number }}
												</a>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm text-gray-900 dark:text-white">{{ $order->customer_name ?? 'N/A' }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span
													class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y H:i') }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span
													class="text-sm font-semibold text-gray-900 dark:text-white">৳{{ number_format($order->total_amount, 2) }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												@php $statusColors = ['pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'shipped' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400', 'delivered' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400']; @endphp
												<span
													class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400' }}">
													{{ ucfirst($order->status) }}
												</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												@php $paymentColors = ['pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'paid' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400']; @endphp
												<span
													class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400' }}">
													{{ ucfirst($order->payment_status) }}
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			@endif
		</div>
	</div>
@endsection
