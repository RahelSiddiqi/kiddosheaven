@extends('admin.layouts.app')

@section('title', 'Products — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Header -->
				<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Products</h3>
					</div>
					<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
						<a href="{{ route('admin.products.create') }}"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
							<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
							Add Product
						</a>
					</div>
				</div>

				@if ($products->isEmpty())
					<div class="px-5 pb-5">
						<p class="text-gray-500 dark:text-gray-400">No products yet. <a href="{{ route('admin.products.create') }}"
								class="text-[--admin-primary] underline hover:text-[--admin-accent]">Add your first product</a>
						</p>
					</div>
				@else
					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Image</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Name</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											SKU</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Category / Brand</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Type</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Price</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Cost</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Profit</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Stock</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Variants</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Featured</th>
										<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($products as $product)
										<tr>
											<td class="py-4 whitespace-nowrap">
												@php
													$img = $product->primary_image ?? ($product->images[0] ?? null);
												@endphp
												@if ($img)
													<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
												@else
													<div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center text-gray-400">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
															stroke="currentColor">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M3 7v10a4 4 0 004 4h10a4 4 0 004-4V7a4 4 0 00-4-4H7a4 4 0 00-4 4z" />
														</svg>
													</div>
												@endif
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900 dark:text-white">
													{{ $product->name }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-xs font-mono text-gray-600 dark:text-gray-400">{{ $product->sku ?? '—' }}</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">
													{{ $product->category->name ?? '-' }}
												</div>
												<div class="text-xs text-gray-400 dark:text-gray-500">
													{{ $product->brand->name ?? '—' }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<span
													class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-50 text-gray-700 dark:bg-gray-700/50 dark:text-gray-200">
													{{ ucfirst($product->product_type ?? 'simple') }}
												</span>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900 dark:text-white">
													৳{{ number_format($product->price, 2) }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">
													৳{{ number_format($product->cost_price ?? 0, 2) }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												@php
													$profit = $product->profit_margin;
													$colorClass = $profit >= 0 ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500';
												@endphp
												<div class="text-sm font-medium {{ $colorClass }}">
													{{ $profit !== null ? number_format($profit, 1) . '%' : 'N/A' }}
												</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												@php
													$variantsCount = $product->variants?->count() ?? 0;
													$stockTotal =
													    $variantsCount > 0 ? $product->variants->sum('stock_quantity') : $product->stock_quantity ?? 0;
												@endphp
												<div class="text-sm font-medium text-gray-900 dark:text-white">
													{{ number_format($stockTotal) }}
												</div>
												<div class="text-xs text-gray-500 dark:text-gray-400">
													{{ $variantsCount > 0 ? 'Variants total' : 'On hand' }}</div>
											</td>
											<td class="py-4 whitespace-nowrap">
												@if ($variantsCount > 0)
													<span
														class="px-2 inline-flex items-center gap-1 text-xs leading-5 font-semibold rounded-full bg-purple-50 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400">
														<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
														</svg>
														{{ $variantsCount }}
													</span>
												@else
													<span class="text-xs text-gray-400 dark:text-gray-500">—</span>
												@endif
											</td>
											<td class="py-4 whitespace-nowrap">
												@if ($product->is_featured)
													<span
														class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">
														Featured
													</span>
												@else
													<span
														class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
														Standard
													</span>
												@endif
											</td>
											<td class="py-4 text-sm font-medium text-right whitespace-nowrap">
												<div class="flex items-center gap-2 justify-end">
													<a href="{{ route('admin.products.show', $product) }}"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="View">
														<svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
															viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
														</svg>
													</a>
													<a href="{{ route('admin.products.edit', $product) }}"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Edit">
														<x-icons.edit />
													</a>
													<form action="{{ route('admin.products.destroy', $product) }}" method="post" class="inline"
														onsubmit="return confirm('Are you sure you want to delete this product?')">
														@csrf
														@method('DELETE')
														<button type="submit" class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Delete">
															<x-icons.delete />
														</button>
													</form>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					<div class="mt-6 px-5 pb-5">
						{{ $products->links('vendor.pagination.default') }}
					</div>
				@endif
			</div>
		</div>
	</div>

@endsection
