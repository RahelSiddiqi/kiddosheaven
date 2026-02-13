@extends('admin.layouts.app')

@section('title', $category->name . ' — Categories')

@section('content')
	{{-- Header --}}
	<x-admin.ui.entity-header :title="$category->name" :subtitle="$category->description" :badge="$category->is_active ? 'Active' : 'Inactive'" :badgeColor="$category->is_active ? 'green' : 'gray'" :breadcrumbs="collect([
	    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
	    ['label' => 'Categories', 'url' => route('admin.categories.index')],
	])
	    ->merge($breadcrumbs)
	    ->toArray()"
		:backUrl="route('admin.categories.index')" :actions="[
		    [
		        'label' => 'Edit Category',
		        'url' => route('admin.categories.edit', $category),
		        'style' => 'primary',
		        'icon' =>
		            '<svg class=\'w-4 h-4 mr-1.5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\'/></svg>',
		    ],
		]" />

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		{{-- Main Content --}}
		<div class="lg:col-span-2 space-y-6">
			{{-- Category Details --}}
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Details</h3>
				</div>
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
									class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
									{{ $category->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400' }}">
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

			{{-- Subcategories --}}
			@if ($category->children->isNotEmpty())
				<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
					<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
							Subcategories
							<span class="ml-2 text-sm font-normal text-gray-500">({{ $category->children->count() }})</span>
						</h3>
					</div>
					<div class="p-5">
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
							@foreach ($category->children as $child)
								<a href="{{ route('admin.categories.show', $child) }}"
									class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:border-brand-300 hover:bg-brand-50/50 dark:border-gray-700 dark:hover:border-brand-700 dark:hover:bg-brand-500/5 transition-colors">
									<div class="flex items-center gap-3">
										@if ($child->icon)
											<span class="text-2xl">{{ $child->icon }}</span>
										@else
											<div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
												<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
												</svg>
											</div>
										@endif
										<div>
											<p class="font-medium text-gray-900 dark:text-white">{{ $child->name }}</p>
											<p class="text-sm text-gray-500">{{ $child->products_count ?? 0 }} products</p>
										</div>
									</div>
									<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
									</svg>
								</a>
							@endforeach
						</div>
					</div>
				</div>
			@endif

			{{-- Products in Category --}}
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
						Products
						<span class="ml-2 text-sm font-normal text-gray-500">({{ $category->products->count() }})</span>
					</h3>
					<a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
						class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
						View all →
					</a>
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
						<div class="space-y-3">
							@foreach ($category->products->take(10) as $product)
								<a href="{{ route('admin.products.show', $product) }}"
									class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:border-brand-300 hover:bg-brand-50/50 dark:border-gray-700 dark:hover:border-brand-700 dark:hover:bg-brand-500/5 transition-colors">
									<div class="flex items-center gap-4">
										@if ($product->image_url)
											<img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover">
										@else
											<div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
												<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
												</svg>
											</div>
										@endif
										<div>
											<p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
											<p class="text-sm text-gray-500">{{ $product->sku ?? 'No SKU' }}</p>
										</div>
									</div>
									<div class="text-right">
										<p class="font-medium text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</p>
										<p class="text-sm text-gray-500">{{ $product->variants_count ?? 0 }} variants</p>
									</div>
								</a>
							@endforeach
							@if ($category->products->count() > 10)
								<a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
									class="block text-center py-3 text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
									View all {{ $category->products->count() }} products →
								</a>
							@endif
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

			{{-- Actions --}}
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actions</h3>
				</div>
				<div class="p-5 space-y-3">
					<a href="{{ route('admin.categories.edit', $category) }}"
						class="block w-full px-4 py-2 text-center text-sm font-medium text-white bg-brand-500 hover:bg-brand-600 rounded-lg transition-colors">
						Edit Category
					</a>
					<a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}"
						class="block w-full px-4 py-2 text-center text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 rounded-lg transition-colors">
						Add Product
					</a>
					<form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
						onsubmit="return confirm('Are you sure you want to delete this category? All products will be unassigned.')">
						@csrf
						@method('DELETE')
						<button type="submit"
							class="block w-full px-4 py-2 text-center text-sm font-medium text-white bg-error-500 hover:bg-error-600 rounded-lg transition-colors">
							Delete Category
						</button>
					</form>
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
