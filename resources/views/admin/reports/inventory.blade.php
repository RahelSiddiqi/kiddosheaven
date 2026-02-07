@extends('admin.layouts.app')

@section('title', 'Inventory Report — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Products</p>
					<p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalProducts) }}</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Stock</p>
					<p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($totalStock) }}</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock</p>
					<p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ number_format($lowStockCount) }}</p>
				</div>
				<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/3">
					<p class="text-sm font-medium text-gray-500 dark:text-gray-400">Out of Stock</p>
					<p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ number_format($outOfStockCount) }}</p>
				</div>
			</div>

			<!-- Filter Form -->
			<form method="GET"
				class="rounded-2xl border border-gray-200 bg-white p-4 mb-6 dark:border-gray-800 dark:bg-white/3">
				<div class="flex flex-wrap items-end gap-4">
					<div class="w-48">
						<label for="category" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category</label>
						<select name="category" id="category"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">All Categories</option>
							@foreach (\App\Models\Catalog::all() as $catalog)
								<option value="{{ $catalog->id }}" {{ request('category') == $catalog->id ? 'selected' : '' }}>
									{{ $catalog->name }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="w-40">
						<label for="stock_status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock
							Status</label>
						<select name="stock_status" id="stock_status"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							<option value="">All Status</option>
							<option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
							<option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock (1-10)</option>
							<option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
							</option>
						</select>
					</div>
					<button type="submit"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
						Filter
					</button>
					<a href="{{ route('admin.reports.inventory') }}"
						class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
						Reset
					</a>
				</div>
			</form>

			@if ($products->isEmpty())
				<div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/3">
					<svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
							d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
					</svg>
					<p class="text-sm font-medium text-gray-900 dark:text-white">No products found</p>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No products found for the selected filters.</p>
				</div>
			@else
				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Inventory Report</h3>
							<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Stock levels across all products</p>
						</div>
						<a href="{{ route('admin.inventory.index') }}"
							class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
							Manage Inventory
						</a>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Product</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Category</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											SKU</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Price</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Stock Level</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Value</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($products as $product)
										<tr>
											<td class="py-4">
												<div class="flex items-center gap-3">
													@if ($product->image)
														<img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
															class="w-10 h-10 rounded-lg object-cover">
													@else
														<div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
															<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																	d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
															</svg>
														</div>
													@endif
													<div>
														<a href="{{ route('admin.products.show', $product) }}"
															class="text-sm font-semibold text-gray-900 dark:text-white hover:text-blue-600">
															{{ $product->name }}
														</a>
													</div>
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm text-gray-600 dark:text-gray-400">{{ $product->catalog->name ?? 'N/A' }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm text-gray-500 dark:text-gray-400">{{ $product->sku }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span
													class="text-sm font-semibold text-gray-900 dark:text-white">৳{{ number_format($product->price, 2) }}</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="flex items-center gap-2">
													@if ($product->stock <= 0)
														<span class="text-sm font-medium text-red-600 dark:text-red-400">0</span>
														<span
															class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
															Out
														</span>
													@elseif ($product->stock <= 10)
														<span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">{{ $product->stock }}</span>
														<span
															class="px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
															Low
														</span>
													@else
														<span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $product->stock }}</span>
														<span
															class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
															Good
														</span>
													@endif
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span class="text-sm font-semibold text-gray-900 dark:text-white">
													৳{{ number_format($product->stock * $product->price, 2) }}
												</span>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>

					<!-- Total Value Footer -->
					<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
						<div class="flex justify-end">
							<div class="text-right">
								<p class="text-sm text-gray-500 dark:text-gray-400">Total Inventory Value</p>
								<p class="text-xl font-bold text-gray-900 dark:text-white">৳{{ number_format($totalValue, 2) }}</p>
							</div>
						</div>
					</div>
				</div>
			@endif
		</div>
	</div>
@endsection
