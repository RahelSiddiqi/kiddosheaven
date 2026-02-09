@extends('admin.layouts.app')

@section('title', 'Product-wise Profit Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Header -->
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 dark:border-gray-800">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Product-wise Profit Report</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $startDate->format('M d, Y') }} -
							{{ $endDate->format('M d, Y') }}</p>
					</div>
					<form method="GET" class="flex gap-2 items-end">
						<div>
							<label for="start_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Start</label>
							<input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<div>
							<label for="end_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">End</label>
							<input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<button type="submit"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
							Filter
						</button>
					</form>
				</div>

				<!-- Summary Stats -->
				<div class="px-6 py-4">
					<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
							<div class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</div>
							<div class="mt-1 font-bold text-blue-600 text-title-sm">{{ number_format($totalRevenue, 2) }} BDT</div>
						</div>
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
							<div class="text-sm text-gray-500 dark:text-gray-400">Total Cost</div>
							<div class="mt-1 font-bold text-red-600 text-title-sm">{{ number_format($totalCost, 2) }} BDT</div>
						</div>
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
							<div class="text-sm text-gray-500 dark:text-gray-400">Total Profit</div>
							<div class="mt-1 font-bold text-green-600 text-title-sm">{{ number_format($totalProfit, 2) }} BDT</div>
						</div>
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
							<div class="text-sm text-gray-500 dark:text-gray-400">Profit Margin</div>
							<div class="mt-1 font-bold text-purple-600 text-title-sm">
								{{ $totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 1) : 0 }}%
							</div>
						</div>
					</div>
				</div>

				<!-- Table -->
				<div class="overflow-hidden">
					<div class="max-w-full overflow-x-auto">
						<table class="min-w-full">
							<thead>
								<tr class="border-gray-200 border-y dark:border-gray-700">
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Product</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Catalog</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Qty
										Sold</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Revenue</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Cost
									</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Profit</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Margin</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
								@foreach ($report as $item)
									@php $margin = $item['total_revenue'] > 0 ? ($item['gross_profit'] / $item['total_revenue']) * 100 : 0; @endphp
									<tr>
										<td class="px-4 py-3">
											<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</div>
										</td>
										<td class="px-4 py-3">
											<span class="text-sm text-gray-500 dark:text-gray-400">{{ $item['catalog_name'] }}</span>
										</td>
										<td class="px-4 py-3">
											<div class="text-sm text-gray-900 dark:text-white">{{ $item['quantity_sold'] }}</div>
										</td>
										<td class="px-4 py-3">
											<div class="text-sm font-medium text-blue-600">{{ number_format($item['total_revenue'], 2) }}</div>
										</td>
										<td class="px-4 py-3">
											<div class="text-sm text-red-600">{{ number_format($item['total_cost'], 2) }}</div>
										</td>
										<td class="px-4 py-3">
											<div class="text-sm font-bold text-green-600">{{ number_format($item['gross_profit'], 2) }}</div>
										</td>
										<td class="px-4 py-3">
											<span
												class="px-2 py-1 text-xs font-semibold rounded-full {{ $margin >= 20 ? 'bg-green-100 text-green-600' : ($margin >= 10 ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') }}">
												{{ number_format($margin, 1) }}%
											</span>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
