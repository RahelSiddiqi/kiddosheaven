@extends('admin.layouts.app')

@section('title', 'Category-wise Profit Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<x-admin.ui.entity-header title="Category-wise Profit" :breadcrumbs="[
			    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
			    ['label' => 'Reports', 'url' => route('admin.reports.index')],
			    ['label' => 'Category Profit'],
			]">
				<x-slot:badge>
					<span
						class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
						{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
					</span>
				</x-slot:badge>
			</x-admin.ui.entity-header>

			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Filter Form -->
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 dark:border-gray-800">
					<form method="GET" class="flex gap-2 items-end">
						<select name="category_id" onchange="this.form.submit()"
							class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">All Categories</option>
							@foreach ($categories as $category)
								<option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
									{{ $category->name }}
								</option>
							@endforeach
						</select>
						<div>
							<input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<div>
							<input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
						</div>
						<button type="submit"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
							Filter
						</button>
					</form>
				</div>

				@if ($report)
					<!-- Summary Stats -->
					<div class="px-6 py-4">
						<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
							<x-admin.ui.stat-card label="Total Revenue" value="৳{{ number_format($report['total_revenue'], 2) }}"
								icon="dollar" color="blue" />
							<x-admin.ui.stat-card label="Total Cost" value="৳{{ number_format($report['total_cost'], 2) }}"
								icon="trending-down" color="red" />
							<x-admin.ui.stat-card label="Total Profit" value="৳{{ number_format($report['gross_profit'], 2) }}"
								icon="trending-up" color="green" />
							<x-admin.ui.stat-card label="Profit Margin" :value="($report['total_revenue'] > 0
							    ? number_format(($report['gross_profit'] / $report['total_revenue']) * 100, 1)
							    : 0) . '%'" icon="chart" color="purple" />
						</div>
					</div>

					<!-- Category Info -->
					@if ($selectedCategory)
						<div class="px-6 pb-4">
							<div class="rounded-xl border border-gray-200 bg-blue-50 p-4 dark:border-blue-700/50 dark:bg-blue-500/10">
								<h4 class="font-semibold text-blue-800 dark:text-blue-200">{{ $selectedCategory->name }}</h4>
								<p class="text-sm text-blue-600 dark:text-blue-400">{{ $selectedCategory->products_count ?? 0 }} products in
									this
									category</p>
							</div>
						</div>
					@endif

					<!-- Top Products Table -->
					<div class="px-6 pb-5">
						<h4 class="font-semibold text-gray-800 dark:text-white/90 mb-3">Top Products by Profit</h4>
						<div class="max-w-full overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Product</th>
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
									@foreach ($report['top_products'] as $product)
										@php $margin = $product['revenue'] > 0 ? ($product['profit'] / $product['revenue']) * 100 : 0; @endphp
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors"
											onclick="window.location='{{ route('admin.products.show', $product['product_id']) }}'">
											<td class="px-4 py-3">
												<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product['name'] }}</div>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm text-gray-500 dark:text-gray-400">{{ $product['quantity_sold'] }}</div>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm font-medium text-blue-600">৳{{ number_format($product['revenue'], 2) }}</div>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm text-red-600">৳{{ number_format($product['cost'], 2) }}</div>
											</td>
											<td class="px-4 py-3">
												<div class="text-sm font-bold {{ $product['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
													৳{{ number_format($product['profit'], 2) }}</div>
											</td>
											<td class="px-4 py-3">
												<span
													class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $margin >= 30 ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : ($margin >= 15 ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400') }}">
													{{ number_format($margin, 1) }}%
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@else
					<div class="px-6 py-12 text-center">
						<svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
						</svg>
						<h4 class="text-lg font-medium text-gray-800 dark:text-white/90">Select a Category</h4>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose a category to view profit breakdown</p>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
