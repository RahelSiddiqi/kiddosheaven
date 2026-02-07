@extends('admin.layouts.app')

@section('title', 'Stock Alerts — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<!-- Header -->
			<div class="mb-4">
				<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Stock Alerts</h3>
				<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Products that need your attention.</p>
			</div>

			@if ($alerts->isEmpty())
				<div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/3">
					<svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
							d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					<p class="text-sm font-medium text-gray-900 dark:text-white">All stocked up!</p>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No low stock or out of stock items.</p>
				</div>
			@else
				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Product</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Current Stock</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Alert Level</th>
										<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($alerts as $product)
										<tr>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm text-gray-900 dark:text-white">{{ $product->stock_quantity }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												@if ($product->stock_quantity <= 0)
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Out
														of Stock</span>
												@else
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Low
														Stock</span>
												@endif
											</td>
											<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
												<a href="{{ route('admin.inventory.index') }}"
													class="h-9 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
													Update Stock
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
		</div>
	</div>
@endsection
