@extends('admin.layouts.app')

@section('title', $category->name . ' — Categories')

@section('content')
	@php
		$headerActions = [
		    [
		        'label' => 'Edit',
		        'url' => route('admin.categories.edit', $category),
		        'style' => 'ghost',
		        'icon' =>
		            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>',
		        'attributes' => ['title' => 'Edit Category'],
		    ],
		    [
		        'label' => 'Add Product',
		        'url' => route('admin.products.create', ['category_id' => $category->id]),
		        'style' => 'primary',
		        'icon' =>
		            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>',
		    ],
		    [
		        'label' => 'Delete',
		        'url' => '#',
		        'style' => 'danger',
		        'icon' =>
		            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>',
		        'attributes' => [
		            'onclick' =>
		                "event.preventDefault(); if(confirm('Are you sure you want to delete this category? All products will be unassigned.')) document.getElementById('delete-category-form').submit();",
		            'title' => 'Delete Category',
		        ],
		    ],
		];
	@endphp

	{{-- Header --}}
	<x-admin.ui.entity-header :title="$category->name" :subtitle="$category->description" :badge="$category->is_active ? 'Active' : 'Inactive'" :badgeColor="$category->is_active ? 'green' : 'gray'" :breadcrumbs="collect([
	    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
	    ['label' => 'Categories', 'url' => route('admin.categories.index')],
	])
	    ->merge($breadcrumbs)
	    ->toArray()"
		:backUrl="route('admin.categories.index')" :actions="$headerActions" />

	<form id="delete-category-form" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="hidden">
		@csrf
		@method('DELETE')
	</form>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
		<div class="lg:col-span-2 space-y-6">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="p-5 space-y-4">
					<div class="grid grid-cols-2 gap-4">
						<div>
							<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</label>
							<p class="mt-1 text-base text-gray-900 dark:text-white">{{ $category->name }}</p>
						</div>
						<div>
							<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
							<p class="mt-1">
								<span
									class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400' }}">
									{{ $category->is_active ? 'Active' : 'Inactive' }}
								</span>
							</p>
						</div>
					</div>

					@if ($category->description)
						<div>
							<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</label>
							<p class="mt-1 text-base text-gray-900 dark:text-white">{{ $category->description }}</p>
						</div>
					@endif

					@if ($category->icon)
						<div>
							<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Icon</label>
							<p class="mt-1 text-2xl">{{ $category->icon }}</p>
						</div>
					@endif

					@if ($category->parent)
						<div>
							<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Parent Category</label>
							<p class="mt-1">
								<a href="{{ route('admin.categories.show', $category->parent) }}"
									class="inline-flex items-center text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
									{{ $category->parent->name }}
									<svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
									</svg>
								</a>
							</p>
						</div>
					@endif

					<div class="grid grid-cols-2 gap-4">
						<div>
							<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Show on Home</label>
							<p class="mt-1 text-base text-gray-900 dark:text-white">{{ $category->show_on_home ? 'Yes' : 'No' }}</p>
						</div>
						<div>
							<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Sort Order</label>
							<p class="mt-1 text-base text-gray-900 dark:text-white">{{ $category->sort_order ?? 'Default' }}</p>
						</div>
					</div>
				</div>
			</div>

			{{-- Attributes --}}
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Attributes</h3>
					<div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
						<span
							class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-300">Variant:
							{{ $variantAttributes->count() }}</span>
						<span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Other:
							{{ $otherAttributes->count() }}</span>
					</div>
				</div>
				@php $allAttributes = $variantAttributes->concat($otherAttributes); @endphp
				<div class="p-5 space-y-4">
					@if ($allAttributes->isEmpty())
						<div class="text-center py-8">
							<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
							</svg>
							<p class="mt-2 text-sm text-gray-500">No attributes are linked to this category.</p>
							<a href="{{ route('admin.attributes.index') }}"
								class="mt-3 inline-flex items-center text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">Manage
								attributes</a>
						</div>
					@else
						<div class="overflow-x-auto">
							<table class="min-w-full text-sm">
								<thead class="bg-gray-50 dark:bg-gray-800/50">
									<tr class="text-left text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wide">
										<th class="px-4 py-3">Name</th>
										<th class="px-4 py-3">Type</th>
										<th class="px-4 py-3">Use</th>
										<th class="px-4 py-3">Values</th>
										<th class="px-4 py-3 text-right">Actions</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
									@foreach ($allAttributes as $attribute)
										<tr>
											<td class="px-4 py-3 text-gray-900 dark:text-white">{{ $attribute->name }}</td>
											<td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ ucfirst($attribute->type) }}</td>
											<td class="px-4 py-3">
												<div class="flex items-center gap-2 text-[11px] font-semibold">
													@if ($attribute->use_for_variants)
														<span
															class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-200">Variant</span>
													@endif
													@if ($attribute->pivot?->is_required)
														<span
															class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-200">Required</span>
													@endif
													@if (!$attribute->use_for_variants && !$attribute->pivot?->is_required)
														<span
															class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Additional</span>
													@endif
												</div>
											</td>
											<td class="px-4 py-3 text-gray-600 dark:text-gray-300">
												{{ $attribute->values_count ?? $attribute->values->count() }}</td>
											<td class="px-4 py-3">
												<div class="flex justify-end gap-2">
													<a href="{{ route('admin.attributes.show', $attribute) }}"
														class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="View Attribute">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
														</svg>
													</a>
													<a href="{{ route('admin.attributes.edit', $attribute) }}"
														class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Edit Attribute">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
														</svg>
													</a>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
				</div>
			</div>

			{{-- Subcategories --}}
			@if ($category->children->isNotEmpty())
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subcategories <span
								class="ml-2 text-sm font-normal text-gray-500">({{ $category->children->count() }})</span></h3>
					</div>
					<div class="p-5 overflow-x-auto">
						<table class="min-w-full text-sm">
							<thead class="bg-gray-50 dark:bg-gray-800/50">
								<tr class="text-left text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wide">
									<th class="px-4 py-3">Name</th>
									<th class="px-4 py-3">Products</th>
									<th class="px-4 py-3">Status</th>
									<th class="px-4 py-3 text-right">Actions</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
								@foreach ($category->children as $child)
									<tr>
										<td class="px-4 py-3 text-gray-900 dark:text-white">
											<div class="flex items-center gap-2">
												@if ($child->icon)
													<span class="text-xl">{{ $child->icon }}</span>
												@else
													<div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
														<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
														</svg>
													</div>
												@endif
												<a href="{{ route('admin.categories.show', $child) }}"
													class="font-medium hover:text-brand-600 dark:hover:text-brand-400">{{ $child->name }}</a>
											</div>
										</td>
										<td class="px-4 py-3 text-gray-600 dark:text-gray-300">
											{{ $child->total_products ?? ($child->products_count ?? ($child->product_count ?? ($child->productCount ?? 0))) }}
										</td>
										<td class="px-4 py-3">
											<span
												class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full {{ $child->is_active ? 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">{{ $child->is_active ? 'Active' : 'Inactive' }}</span>
										</td>
										<td class="px-4 py-3">
											<div class="flex justify-end gap-2">
												<a href="{{ route('admin.categories.show', $child) }}"
													class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="View Subcategory">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
													</svg>
												</a>
												<a href="{{ route('admin.categories.edit', $child) }}"
													class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Edit Subcategory">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
													</svg>
												</a>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			@endif

			{{-- Products in Category --}}
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Products <span
							class="ml-2 text-sm font-normal text-gray-500">({{ $category->products->count() }})</span></h3>
					<a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
						class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">View all →</a>
				</div>
				<div class="p-5">
					@if ($category->products->isEmpty())
						<div class="text-center py-8">
							<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
							</svg>
							<p class="mt-2 text-sm text-gray-500">No products in this category yet.</p>
						</div>
					@else
						<div class="overflow-x-auto">
							<table class="min-w-full text-sm">
								<thead class="bg-gray-50 dark:bg-gray-800/50">
									<tr class="text-left text-gray-500 dark:text-gray-400 uppercase text-[11px] tracking-wide">
										<th class="px-4 py-3">Name</th>
										<th class="px-4 py-3">SKU</th>
										<th class="px-4 py-3">Price</th>
										<th class="px-4 py-3">Variants</th>
										<th class="px-4 py-3 text-right">Actions</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
									@foreach ($category->products->take(10) as $product)
										<tr>
											<td class="px-4 py-3 text-gray-900 dark:text-white">
												<div class="flex items-center gap-3">
													@if ($product->image_url)
														<img src="{{ $product->image_url }}" alt="{{ $product->name }}"
															class="w-10 h-10 rounded-lg object-cover">
													@else
														<div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
															<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																	d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
															</svg>
														</div>
													@endif
													<div>
														<a href="{{ route('admin.products.show', $product) }}"
															class="font-medium hover:text-brand-600 dark:hover:text-brand-400">{{ $product->name }}</a>
														<p class="text-xs text-gray-500">{{ $product->category->name ?? 'Unassigned' }}</p>
													</div>
												</div>
											</td>
											<td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $product->sku ?? '—' }}</td>
											<td class="px-4 py-3 text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</td>
											<td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $product->variants_count ?? 0 }}</td>
											<td class="px-4 py-3">
												<div class="flex justify-end gap-2">
													<a href="{{ route('admin.products.show', $product) }}"
														class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="View Product">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
														</svg>
													</a>
													<a href="{{ route('admin.products.edit', $product) }}"
														class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="Edit Product">
														<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
														</svg>
													</a>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
								@if ($category->products->count() > 10)
									<tfoot>
										<tr>
											<td colspan="5" class="px-4 py-3 text-right">
												<a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
													class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">View all
													{{ $category->products->count() }} products →</a>
											</td>
										</tr>
									</tfoot>
								@endif
							</table>
						</div>
					@endif
				</div>
			</div>
		</div>

		{{-- Sidebar --}}
		<div class="space-y-6">
			{{-- Stats --}}
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Statistics</h3>
				</div>
				<div class="p-5 space-y-4">
					<div>
						<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Products</label>
						<p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalProducts }}</p>
					</div>
					<div>
						<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Direct Products</label>
						<p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $category->products->count() }}</p>
					</div>
					<div>
						<label class="text-sm font-medium text-gray-500 dark:text-gray-400">Subcategories</label>
						<p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $category->children->count() }}</p>
					</div>
				</div>
			</div>

			{{-- Metadata --}}
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Metadata</h3>
				</div>
				<div class="p-5 space-y-3 text-sm">
					<div>
						<label class="text-gray-500 dark:text-gray-400">Created</label>
						<p class="text-gray-900 dark:text-white">{{ $category->created_at->format('M d, Y') }}</p>
					</div>
					<div>
						<label class="text-gray-500 dark:text-gray-400">Last Updated</label>
						<p class="text-gray-900 dark:text-white">{{ $category->updated_at->format('M d, Y') }}</p>
					</div>
					<div>
						<label class="text-gray-500 dark:text-gray-400">Category ID</label>
						<p class="text-gray-900 dark:text-white font-mono">{{ $category->id }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
