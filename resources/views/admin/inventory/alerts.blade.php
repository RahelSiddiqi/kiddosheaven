@extends('admin.layouts.app')

@section('title', 'Stock Alerts — Kiddo\'s Heaven')

@section('content')
	<!-- Entity Header -->
	<x-admin.ui.entity-header title="Stock Alerts" :breadcrumbs="[
	    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
	    ['label' => 'Inventory', 'url' => route('admin.inventory.index')],
	    ['label' => 'Alerts', 'url' => null],
	]">
	</x-admin.ui.entity-header>

	@if ($alerts->isEmpty())
		<div class="rounded-2xl border border-gray-200 bg-white p-16 text-center dark:border-gray-800 dark:bg-white/3">
			<x-admin.ui.empty-state icon="check" title="All stocked up!"
				description="No low stock or out of stock items at the moment." :showAction="false" />
		</div>
	@else
		<!-- Alert Summary Cards -->
		<div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2">
			@php
				$outOfStockCount = $alerts->where('stock_quantity', 0)->count();
				$lowStockCount = $alerts->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count();
			@endphp

			<x-admin.ui.stat-card title="Out of Stock" :value="$outOfStockCount" icon="x-circle" color="red" />

			<x-admin.ui.stat-card title="Low Stock" :value="$lowStockCount" icon="alert" color="yellow" />
		</div>

		<!-- Alerts Table -->
		<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
			<div class="overflow-hidden">
				<div class="max-w-full overflow-x-auto">
					<table class="min-w-full">
						<thead>
							<tr class="border-gray-200 border-y dark:border-gray-700">
								<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
									Product</th>
								<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
									Current Stock</th>
								<th scope="col" class="px-6 py-3.5 font-medium text-gray-700 text-start text-sm dark:text-gray-300">
									Alert Level</th>
								<th scope="col" class="relative px-6 py-3.5"><span class="sr-only">Actions</span></th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
							@foreach ($alerts as $product)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors"
									onclick="window.location='{{ route('admin.products.show', $product) }}'">
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
										<div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku ?? 'N/A' }}</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<span
											class="text-sm font-semibold {{ $product->stock_quantity <= 0 ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400' }}">
											{{ $product->stock_quantity }}
										</span>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										@if ($product->stock_quantity <= 0)
											<span
												class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400">
												<span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-red-400"></span>
												Out of Stock
											</span>
										@else
											<span
												class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400">
												<span class="w-1.5 h-1.5 rounded-full bg-yellow-600 dark:bg-yellow-400"></span>
												Low Stock
											</span>
										@endif
									</td>
									<td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
										<a href="{{ route('admin.products.show', $product) }}" @click.stop
											class="inline-flex items-center gap-2 rounded-lg border border-blue-500 bg-blue-500 px-3 py-2 text-xs font-medium text-white shadow-sm hover:bg-blue-600 transition-colors">
											View Details
											<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
											</svg>
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	@endif
@endsection
