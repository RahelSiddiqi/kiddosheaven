@extends('admin.layouts.app')

@section('title', 'Product-wise Profit Report — Kiddo\'s Heaven')

@section('content')
	<!-- Entity Header -->
	<x-admin.ui.entity-header title="Product-wise Profit Report" :breadcrumbs="[
	    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
	    ['label' => 'Reports', 'url' => route('admin.reports.index')],
	    ['label' => 'Product Profit', 'url' => null],
	]">
		<x-slot:badge>
			<span class="text-sm font-medium text-gray-600 dark:text-gray-400">
				{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
			</span>
		</x-slot:badge>
	</x-admin.ui.entity-header>

	<!-- Stats Grid -->
	<div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
		<x-admin.ui.stat-card label="Total Revenue" :value="'৳' . number_format($totalRevenue, 2)" icon="dollar" color="blue" />

		<x-admin.ui.stat-card label="Total Cost" :value="'৳' . number_format($totalCost, 2)" icon="trending-down" color="red" />

		<x-admin.ui.stat-card label="Total Profit" :value="'৳' . number_format($totalProfit, 2)" icon="trending-up" color="green" />

		<x-admin.ui.stat-card label="Profit Margin" :value="($totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 1) : '0') . '%'" icon="chart" color="purple" />
	</div>

	<!-- Date Filter -->
	<div class="rounded-2xl border border-gray-200 bg-white p-4 mb-6 dark:border-gray-800 dark:bg-white/3">
		<form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:flex xl:flex-nowrap gap-3 items-end">
			<div class="flex-1 min-w-[160px]">
				<label for="start_date" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
				<input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
					class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-600">
			</div>
			<div class="flex-1 min-w-[160px]">
				<label for="end_date" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
				<input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
					class="h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-600">
			</div>
			<button type="submit"
				class="h-11 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-600 transition-colors">
				Apply Filter
			</button>
		</form>
	</div>

	<!-- Products Table -->
	<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
		<div class="overflow-hidden">
			<div class="max-w-full overflow-x-auto">
				<table class="min-w-full">
					<thead>
						<tr class="border-gray-200 border-y dark:border-gray-700">
							<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
								Product</th>
							<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
								Category</th>
							<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">Qty Sold
							</th>
							<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
								Revenue</th>
							<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">Cost</th>
							<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
								Profit</th>
							<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
								Margin</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@foreach ($report as $item)
							@php
								$margin = $item['total_revenue'] > 0 ? ($item['gross_profit'] / $item['total_revenue']) * 100 : 0;
							@endphp
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors"
								onclick="window.location='{{ route('admin.products.show', $item['id']) }}'">
								<td class="px-6 py-4">
									<a href="{{ route('admin.products.show', $item['id']) }}" @click.stop
										class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">
										{{ $item['name'] }}
									</a>
								</td>
								<td class="px-6 py-4">
									<span class="text-sm text-gray-500 dark:text-gray-400">{{ $item['category_name'] }}</span>
								</td>
								<td class="px-6 py-4">
									<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['quantity_sold'] }}</div>
								</td>
								<td class="px-6 py-4">
									<div class="text-sm font-semibold text-blue-600 dark:text-blue-400">
										৳{{ number_format($item['total_revenue'], 2) }}</div>
								</td>
								<td class="px-6 py-4">
									<div class="text-sm font-semibold text-red-600 dark:text-red-400">৳{{ number_format($item['total_cost'], 2) }}
									</div>
								</td>
								<td class="px-6 py-4">
									<div
										class="text-sm font-semibold {{ $item['gross_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
										৳{{ number_format($item['gross_profit'], 2) }}
									</div>
								</td>
								<td class="px-6 py-4">
									<div
										class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $margin >= 30 ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : ($margin >= 15 ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400') }}">
										{{ number_format($margin, 1) }}%
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
