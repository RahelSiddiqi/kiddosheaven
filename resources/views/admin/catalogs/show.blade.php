@extends('admin.layouts.app')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<!-- Back Button & Header -->
		<div class="col-span-12">
			<div class="flex items-center gap-4 mb-6">
				<a href="{{ route('admin.catalogs.index') }}"
					class="flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
					</svg>
					Back to Catalogs
				</a>
			</div>
		</div>

		<!-- Catalog Info Card -->
		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<div class="flex items-start justify-between mb-4">
					<div>
						<h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $catalog->name }}</h2>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
							@php
								$typeOptions = [
								    'general' => 'General',
								    'grocery' => 'Grocery',
								    'clothing' => 'Clothing & Apparel',
								    'toys' => 'Toys & Games',
								    'puzzles' => 'Puzzles & Brain Teasers',
								    'food' => 'Food & Beverages',
								    'electronics' => 'Electronics',
								    'home' => 'Home & Garden',
								    'beauty' => 'Beauty & Personal Care',
								    'sports' => 'Sports & Outdoors',
								    'books' => 'Books & Media',
								    'baby' => 'Baby Products',
								    'health' => 'Health & Wellness',
								];
							@endphp
							{{ $typeOptions[$catalog->type] ?? 'General' }}
						</p>
					</div>
					@if ($catalog->show_on_home)
						<span
							class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">
							Active
						</span>
					@else
						<span
							class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
							Inactive
						</span>
					@endif
				</div>

				<!-- Stats -->
				<div class="grid grid-cols-2 gap-4 mb-6">
					<div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800">
						<p class="text-sm text-gray-500 dark:text-gray-400">Products</p>
						<p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $catalog->products_count }}</p>
					</div>
					<div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800">
						<p class="text-sm text-gray-500 dark:text-gray-400">Attributes</p>
						<p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $catalog->attributes->count() }}</p>
					</div>
				</div>

				<!-- Meta Info -->
				<div class="space-y-3 text-sm">
					<div class="flex justify-between">
						<span class="text-gray-500 dark:text-gray-400">Created</span>
						<span class="text-gray-800 dark:text-white">{{ $catalog->created_at?->format('M d, Y') ?? 'N/A' }}</span>
					</div>
					<div class="flex justify-between">
						<span class="text-gray-500 dark:text-gray-400">Last Updated</span>
						<span class="text-gray-800 dark:text-white">{{ $catalog->updated_at?->format('M d, Y') ?? 'N/A' }}</span>
					</div>
				</div>

				<!-- Actions -->
				<div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
					<a href="{{ route('admin.catalogs.attributes.index', [$catalog->id]) }}"
						class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
						<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
						</svg>
						Manage Attributes
					</a>
					<a href="{{ route('admin.products.index', ['catalog' => $catalog->id]) }}"
						class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
						View Products
					</a>
				</div>
			</div>
		</div>

		<!-- Assigned Attributes -->
		<div class="col-span-12 lg:col-span-8">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Assigned Attributes</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Attributes available for this catalog type</p>
					</div>
					<a href="{{ route('admin.catalogs.attributes.index', [$catalog->id]) }}"
						class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-500/15 dark:text-blue-500 transition-colors">
						<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
						</svg>
						Add Attributes
					</a>
				</div>

				<div class="p-5">
					@if ($catalog->attributes->count() > 0)
						<div class="grid gap-3">
							@foreach ($catalog->attributes as $attribute)
								<div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-800">
									<div class="flex items-center gap-3">
										<div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-500/20">
											@switch($attribute->type)
												@case('text')
													<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
													</svg>
												@break

												@case('select')
													<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
													</svg>
												@break

												@case('multiselect')
													<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M8 9l-2 2 2 2m8-2l2 2-2 2M4 4v16M20 4V4" />
													</svg>
												@break

												@case('boolean')
													<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
													</svg>
												@break

												@case('number')
													<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
													</svg>
												@break

												@case('color')
													<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
													</svg>
												@break

												@default
													<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
													</svg>
											@endswitch
										</div>
										<div>
											<p class="text-sm font-medium text-gray-800 dark:text-white">{{ $attribute->name }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($attribute->type) }} @if ($attribute->required)
													<span class="text-red-500">*</span>
												@endif
											</p>
										</div>
									</div>
									<div class="flex items-center gap-2">
										@if ($attribute->values->count() > 0)
											<span class="px-2 py-1 text-xs rounded-md bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
												{{ $attribute->values->count() }} values
											</span>
										@endif
										<a href="{{ route('admin.attributes.edit', $attribute->id) }}"
											class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
											<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
											</svg>
										</a>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<div class="text-center py-8">
							<div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
								<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
								</svg>
							</div>
							<h4 class="text-sm font-medium text-gray-800 dark:text-white mb-1">No Attributes Assigned</h4>
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add attributes to this catalog to define product
								specifications</p>
							<a href="{{ route('admin.catalogs.attributes.index', [$catalog->id]) }}"
								class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
								<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
								</svg>
								Add Attributes
							</a>
						</div>
					@endif
				</div>
			</div>
		</div>

		<!-- Recent Products -->
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3">
				<div
					class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Recent Products</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Latest products in this catalog</p>
					</div>
					<a href="{{ route('admin.products.index', ['catalog' => $catalog->id]) }}"
						class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-colors">
						View All Products
					</a>
				</div>

				<div class="overflow-x-auto">
					<table class="min-w-full">
						<thead>
							<tr class="border-b border-gray-200 dark:border-gray-700">
								<th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
								<th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
								<th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Price</th>
								<th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
								<th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
							@forelse($catalog->products as $product)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
									<td class="px-5 py-4">
										<a href="{{ route('admin.products.edit', $product->id) }}"
											class="text-sm font-medium text-gray-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
											{{ $product->name }}
										</a>
									</td>
									<td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $product->sku }}</td>
									<td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white">
										${{ number_format($product->price, 2) }}</td>
									<td class="px-5 py-4">
										<span class="text-sm {{ $product->stock_quantity < 10 ? 'text-red-600' : 'text-gray-800 dark:text-white' }}">
											{{ $product->stock_quantity }}
										</span>
									</td>
									<td class="px-5 py-4">
										@if ($product->is_active)
											<span
												class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400">
												Active
											</span>
										@else
											<span
												class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400">
												Inactive
											</span>
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="px-5 py-8 text-center">
										<div class="flex flex-col items-center justify-center">
											<div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
												<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
												</svg>
											</div>
											<h4 class="text-sm font-medium text-gray-800 dark:text-white mb-1">No Products Yet</h4>
											<p class="text-sm text-gray-500 dark:text-gray-400">Add your first product to this catalog</p>
										</div>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection
