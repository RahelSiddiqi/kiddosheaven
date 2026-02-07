@extends('layouts.app')

@section('title', 'Catalog — Kiddo\'s Heaven')

@section('content')
	{{-- Page Header --}}
	<div class="mb-8">
		<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
			<div>
				<h1 class="text-3xl font-bold text-gray-900">Catalog</h1>
				<p class="text-gray-500 mt-1">Browse our collection of safe, fun toys for your little ones</p>
			</div>
			<div class="flex items-center gap-2 text-sm text-gray-500">
				<span>{{ $products->total() }} products</span>
			</div>
		</div>
	</div>

	<div class="flex flex-col lg:flex-row gap-8">
		{{-- Sidebar Filters --}}
		<aside class="lg:w-64 flex-shrink-0">
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
				{{-- Filter Header --}}
				<div class="flex items-center justify-between mb-6">
					<h3 class="font-bold text-gray-900">Filters</h3>
					<a href="{{ route('catalog') }}" class="text-primary text-sm hover:underline">Clear all</a>
				</div>

				{{-- Category Filter --}}
				<div class="mb-6">
					<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
						<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
						</svg>
						Categories
					</h4>
					<div class="space-y-2">
						<a href="{{ route('catalog') }}"
							class="flex items-center justify-between px-3 py-2 rounded-lg transition {{ !$activeCategory ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
							<span>All Products</span>
							<span class="text-xs text-gray-400">{{ $products->total() }}</span>
						</a>
						@foreach ($categories as $category)
							<a href="{{ route('catalog', ['catalog_id' => $category->id]) }}"
								class="flex items-center justify-between px-3 py-2 rounded-lg transition {{ $activeCategory == $category->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
								<span>{{ $category->name }}</span>
								{{-- Note: You'd need to add product count per category --}}
								<span class="text-xs text-gray-400">→</span>
							</a>
						@endforeach
					</div>
				</div>

				{{-- Price Filter --}}
				<div class="mb-6">
					<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
						<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						Price Range
					</h4>
					<div class="space-y-2">
						<a href="{{ route('catalog', array_merge(request()->all(), ['price' => 'under-10'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>Under $10</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['price' => '10-25'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>$10 - $25</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['price' => '25-50'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>$25 - $50</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['price' => 'over-50'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>Over $50</span>
						</a>
					</div>
				</div>

				{{-- Age Group Filter --}}
				<div class="mb-6">
					<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
						<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
						</svg>
						Age Group
					</h4>
					<div class="space-y-2">
						<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '0-2'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>0-2 years</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '3-5'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>3-5 years</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '6-8'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>6-8 years</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '9+'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>9+ years</span>
						</a>
					</div>
				</div>

				{{-- Features Filter --}}
				<div>
					<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
						<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
						</svg>
						Features
					</h4>
					<div class="space-y-2">
						<a href="{{ route('catalog', array_merge(request()->all(), ['featured' => '1'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>Featured</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['new' => '1'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>New Arrivals</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['sale' => '1'])) }}"
							class="flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">
							<span>On Sale</span>
						</a>
					</div>
				</div>
			</div>
		</aside>

		{{-- Products Grid --}}
		<div class="flex-1">
			{{-- Sort Bar --}}
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
				<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
					<div class="flex items-center gap-2 overflow-x-auto">
						@if ($activeCategory)
							@php $category = $categories->firstWhere('id', $activeCategory) @endphp
							@if ($category)
								<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-sm">
									{{ $category->name }}
									<a href="{{ route('catalog') }}" class="hover:text-primary-dark">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
										</svg>
									</a>
								</span>
							@endif
						@endif
						@if (request('price'))
							<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-sm">
								{{ request('price') }}
								<a href="{{ route('catalog', array_merge(request()->all(), ['price' => null])) }}"
									class="hover:text-primary-dark">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</a>
							</span>
						@endif
					</div>
					<div class="flex items-center gap-3">
						<span class="text-sm text-gray-500">Sort by:</span>
						<select onchange="window.location.href = this.value"
							class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
							<option value="{{ route('catalog', array_merge(request()->all(), ['sort' => 'newest'])) }}"
								{{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>
								Newest
							</option>
							<option value="{{ route('catalog', array_merge(request()->all(), ['sort' => 'price-low'])) }}"
								{{ request('sort') == 'price-low' ? 'selected' : '' }}>
								Price: Low to High
							</option>
							<option value="{{ route('catalog', array_merge(request()->all(), ['sort' => 'price-high'])) }}"
								{{ request('sort') == 'price-high' ? 'selected' : '' }}>
								Price: High to Low
							</option>
							<option value="{{ route('catalog', array_merge(request()->all(), ['sort' => 'popular'])) }}"
								{{ request('sort') == 'popular' ? 'selected' : '' }}>
								Most Popular
							</option>
						</select>
					</div>
				</div>
			</div>

			{{-- Products --}}
			@if ($products->count() > 0)
				<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
					@foreach ($products as $product)
						<article
							class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
							<a href="{{ route('products.show', $product->slug) }}">
								<div class="relative w-full aspect-4/3 bg-gray-100 overflow-hidden">
									@php
										$img = $product->primary_image ?? ($product->images[0] ?? null);
									@endphp
									@if ($img)
										<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" loading="lazy"
											class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
									@else
										<div class="w-full h-full flex items-center justify-center">
											<span class="text-4xl">🧸</span>
										</div>
									@endif
									{{-- Badges --}}
									<div class="absolute top-3 left-3 flex flex-col gap-2">
										@if ($product->is_featured)
											<span class="px-2 py-1 rounded-full bg-yellow-400 text-white text-xs font-bold shadow">★</span>
										@endif
										@if ($product->created_at && $product->created_at->diffInDays() < 7)
											<span class="px-2 py-1 rounded-full bg-green-500 text-white text-xs font-bold shadow">New</span>
										@endif
									</div>
									{{-- Quick Add --}}
									<div
										class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition duration-300 translate-y-2 group-hover:translate-y-0">
										<form action="{{ route('cart.add', $product->slug) }}" method="post">
											@csrf
											<button type="submit"
												class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center shadow-lg hover:bg-primary-dark hover:scale-110 transition">
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
												</svg>
											</button>
										</form>
									</div>
								</div>
							</a>
							<div class="p-4">
								@if ($product->catalog)
									<span
										class="inline-block px-2 py-1 rounded-full bg-light text-primary text-xs font-semibold mb-2">{{ $product->catalog->name }}</span>
								@endif
								<a href="{{ route('products.show', $product->slug) }}"
									class="font-semibold text-gray-800 hover:text-primary line-clamp-2 group-hover:text-primary transition">{{ $product->name }}</a>
								<div class="flex items-center justify-between mt-3">
									<span class="text-lg font-bold text-primary-dark">${{ number_format($product->price / 100, 2) }}</span>
									<div class="flex items-center gap-1 text-yellow-400 text-sm">
										<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
											<path
												d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
										</svg>
										<span class="text-gray-400 text-xs">4.5</span>
									</div>
								</div>
							</div>
						</article>
					@endforeach
				</div>

				{{-- Pagination --}}
				@if ($products->hasPages())
					<div class="mt-10">
						{{ $products->links() }}
					</div>
				@endif
			@else
				{{-- Empty State --}}
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
					<span class="text-5xl mb-4 block">🔍</span>
					<h3 class="text-xl font-bold text-gray-800 mb-2">No products found</h3>
					<p class="text-gray-500 mb-6">Try adjusting your filters or browse all products</p>
					<a href="{{ route('catalog') }}"
						class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-bold hover:bg-primary-dark transition">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
						</svg>
						Clear Filters
					</a>
				</div>
			@endif
		</div>
	</div>
@endsection
