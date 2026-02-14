@extends('layouts.app')

@section('title', 'Catalog — Kiddo\'s Heaven')

@section('content')
	<style>
		.category-item.expanded>.category-children {
			display: block !important;
		}

		.category-item.expanded .category-toggle svg {
			transform: rotate(90deg);
		}
	</style>
	{{-- Page Header - Hidden, using layout header instead --}}
	{{-- Mobile Filter Button --}}
	<button id="mobile-filter-toggle"
		class="lg:hidden w-full flex items-center justify-between px-4 py-3 bg-white rounded-xl shadow-sm border border-gray-100 mb-4">
		<div class="flex items-center gap-2">
			<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
			</svg>
			<span class="font-medium">Filters</span>
		</div>
		<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
		</svg>
	</button>

	<div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
		{{-- Mobile Filter Overlay --}}
		<div id="filter-overlay" class="fixed inset-0 bg-black/50 z-50 lg:hidden hidden" onclick="closeFilterDrawer()"></div>

		{{-- Sidebar Filters (Desktop) / Drawer (Mobile) --}}
		<aside id="filter-drawer"
			class="lg:w-64 flex-shrink-0 fixed lg:sticky lg:top-28 inset-y-0 left-0 z-50 lg:z-auto w-80 max-w-[85vw] bg-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 lg:block overflow-y-auto lg:max-h-[calc(100vh-7.5rem)] h-full lg:h-auto pb-20 lg:pb-0">
			<div class="p-6">
				{{-- Mobile Filter Header --}}
				<div class="flex items-center justify-between mb-6 lg:hidden">
					<h3 class="font-bold text-xl text-gray-900">Filters</h3>
					<button onclick="closeFilterDrawer()" class="p-2 hover:bg-gray-100 rounded-full">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>

				{{-- Desktop Filter Header --}}
				<div class="hidden lg:flex items-center justify-between mb-6">
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
					<div class="space-y-1">
						<a href="{{ route('catalog') }}"
							class="flex items-center justify-between px-3 py-2.5 rounded-lg transition text-sm {{ !$activeCategory ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
							<span>All Products</span>
							<span class="text-xs text-gray-400">{{ $products->total() }}</span>
						</a>
						@foreach ($categories as $category)
							<div class="category-item">
								<a href="{{ route('catalog', ['category_id' => $category->id]) }}"
									class="flex items-center justify-between px-3 py-2.5 rounded-lg transition text-sm {{ $activeCategory == $category->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
									<span>{{ $category->name }}</span>
									@if ($category->children && $category->children->count() > 0)
										<span class="category-toggle text-gray-400 hover:text-primary cursor-pointer"
											onclick="event.preventDefault(); event.stopPropagation(); this.closest('.category-item').classList.toggle('expanded')">
											<svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
											</svg>
										</span>
									@else
										<span class="text-xs text-gray-400">→</span>
									@endif
								</a>
								@if ($category->children && $category->children->count() > 0)
									<div class="category-children hidden pl-4 mt-1 space-y-1 border-l-2 border-gray-100 ml-2">
										@foreach ($category->children as $child)
											<a href="{{ route('catalog', ['category_id' => $child->id]) }}"
												class="flex items-center justify-between px-3 py-2 rounded-lg transition text-sm {{ $activeCategory == $child->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }}">
												<span>{{ $child->name }}</span>
												<span class="text-xs text-gray-400">→</span>
											</a>
										@endforeach
									</div>
								@endif
							</div>
						@endforeach
					</div>
				</div>

				{{-- Brand Filter --}}
				@if ($brands->isNotEmpty())
					<div class="mb-6">
						<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
							<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
							</svg>
							Brands
						</h4>
						<div class="space-y-1">
							@foreach ($brands as $brand)
								<a href="{{ route('catalog', array_merge(request()->except('page'), ['brand_id' => $brand->id])) }}"
									class="flex items-center justify-between px-3 py-2.5 rounded-lg transition text-sm {{ $activeBrand == $brand->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
									<span>{{ $brand->name }}</span>
									<span class="text-xs text-gray-400">→</span>
								</a>
							@endforeach
						</div>
					</div>
				@endif

				{{-- Price Filter --}}
				<div class="mb-6">
					<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
						<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						Price Range
					</h4>
					<div class="space-y-1">
						<a href="{{ route('catalog', array_merge(request()->except('page'), ['price' => 'under-10'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg transition text-sm {{ $activePrice == 'under-10' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
							<span>Under ৳1,000</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->except('page'), ['price' => '10-25'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg transition text-sm {{ $activePrice == '10-25' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
							<span>৳1,000 - ৳2,500</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->except('page'), ['price' => '25-50'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg transition text-sm {{ $activePrice == '25-50' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
							<span>৳2,500 - ৳5,000</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->except('page'), ['price' => 'over-50'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg transition text-sm {{ $activePrice == 'over-50' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
							<span>Over ৳5,000</span>
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
					<div class="space-y-1">
						<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '0-2'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
							<span>0-2 years</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '3-5'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
							<span>3-5 years</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '6-8'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
							<span>6-8 years</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '9+'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
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
					<div class="space-y-1">
						<a href="{{ route('catalog', array_merge(request()->all(), ['featured' => '1'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
							<span>Featured</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['new' => '1'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
							<span>New Arrivals</span>
						</a>
						<a href="{{ route('catalog', array_merge(request()->all(), ['sale' => '1'])) }}"
							class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
							<span>On Sale</span>
						</a>
					</div>
				</div>

				{{-- Mobile Apply Button --}}
				<div class="lg:hidden mt-6 pt-4 border-t">
					<button onclick="closeFilterDrawer()" class="w-full btn-primary">
						Apply Filters
					</button>
				</div>
			</div>
		</aside>

		{{-- Products Grid --}}
		<div class="flex-1">
			{{-- Sort Bar --}}
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 md:p-4 mb-4 md:mb-6">
				<div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
					<div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0 scrollbar-hide">
						<span class="text-sm text-gray-500 whitespace-nowrap">{{ $products->total() }} products</span>
						@if ($activeCategory)
							@php $category = $categories->firstWhere('id', $activeCategory) @endphp
							@if ($category)
								<span
									class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary text-sm whitespace-nowrap">
									{{ $category->name }}
									<a href="{{ route('catalog', request()->except(['category_id', 'page'])) }}" class="hover:text-primary-dark">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
										</svg>
									</a>
								</span>
							@endif
						@endif
						@if ($activeBrand)
							@php $brand = $brands->firstWhere('id', $activeBrand) @endphp
							@if ($brand)
								<span
									class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary text-sm whitespace-nowrap">
									{{ $brand->name }}
									<a href="{{ route('catalog', request()->except(['brand_id', 'page'])) }}" class="hover:text-primary-dark">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
										</svg>
									</a>
								</span>
							@endif
						@endif
						@if ($activePrice)
							@php
								$priceLabels = [
								    'under-10' => 'Under ৳1,000',
								    '10-25' => '৳1,000 - ৳2,500',
								    '25-50' => '৳2,500 - ৳5,000',
								    'over-50' => 'Over ৳5,000',
								];
							@endphp
							<span
								class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary text-sm whitespace-nowrap">
								{{ $priceLabels[$activePrice] ?? $activePrice }}
								<a href="{{ route('catalog', request()->except(['price', 'page'])) }}" class="hover:text-primary-dark">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</a>
							</span>
						@endif
						@if (request('featured'))
							<span
								class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary text-sm whitespace-nowrap">
								Featured
								<a href="{{ route('catalog', request()->except(['featured', 'page'])) }}" class="hover:text-primary-dark">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</a>
							</span>
						@endif
						@if (request('new'))
							<span
								class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary text-sm whitespace-nowrap">
								New Arrivals
								<a href="{{ route('catalog', request()->except(['new', 'page'])) }}" class="hover:text-primary-dark">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
									</svg>
								</a>
							</span>
						@endif
					</div>
					<div class="flex items-center gap-2 md:gap-3">
						<span class="text-sm text-gray-500 whitespace-nowrap">Sort:</span>
						<select onchange="window.location.href = this.value"
							class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none min-h-[44px]">
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
				<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3 md:gap-6">
					@foreach ($products as $product)
						<article
							class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
							<a href="{{ route('products.show', $product->slug) }}">
								<div class="relative w-full aspect-[4/3] md:aspect-4/3 bg-gray-100 overflow-hidden">
									@php
										$img = $product->primary_image ?? ($product->images[0] ?? null);
									@endphp
									@if ($img)
										<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" loading="lazy"
											class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
									@else
										<div class="w-full h-full flex items-center justify-center">
											<span class="text-3xl md:text-4xl">🧸</span>
										</div>
									@endif
									{{-- Badges --}}
									<div class="absolute top-2 left-2 flex flex-col gap-1.5">
										@if ($product->is_featured)
											<span
												class="px-2 py-0.5 rounded-full bg-yellow-400 text-white text-[10px] md:text-xs font-bold shadow">★</span>
										@endif
										@if ($product->created_at && $product->created_at->diffInDays() < 7)
											<span
												class="px-2 py-0.5 rounded-full bg-green-500 text-white text-[10px] md:text-xs font-bold shadow">New</span>
										@endif
									</div>
								</div>
							</a>
							<div class="p-3 md:p-4">
								@if ($product->category)
									<span
										class="inline-block px-2 py-0.5 rounded-full bg-gray-100 text-primary text-[10px] md:text-xs font-semibold mb-1 md:mb-2">{{ $product->category->name }}</span>
								@endif
								<a href="{{ route('products.show', $product->slug) }}"
									class="font-semibold text-sm md:text-base text-gray-800 hover:text-primary line-clamp-2 group-hover:text-primary transition">{{ $product->name }}</a>
								<div class="flex items-center justify-between mt-2 md:mt-3">
									<span class="text-base md:text-lg font-bold text-primary-dark">৳{{ number_format($product->price, 2) }}</span>
									@if ($product->review_count > 0)
										<div class="flex items-center gap-1 text-yellow-400 text-xs md:text-sm">
											<svg class="w-3 h-3 md:w-4 md:h-4 fill-current" viewBox="0 0 20 20">
												<path
													d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
											</svg>
											<span class="text-gray-600 text-xs">{{ number_format($product->average_rating, 1) }}</span>
										</div>
									@endif
								</div>
								{{-- Mobile Quick Add Button --}}
								<form action="{{ route('cart.add', $product->slug) }}" method="post" class="mt-3 md:hidden">
									@csrf
									<button type="submit"
										class="w-full py-2.5 rounded-lg bg-primary text-white text-sm font-medium flex items-center justify-center gap-2">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
										</svg>
										Add to Cart
									</button>
								</form>
							</div>
						</article>
					@endforeach
				</div>

				{{-- Pagination --}}
				@if ($products->hasPages())
					<div class="mt-8 md:mt-10">
						{{ $products->links() }}
					</div>
				@endif
			@else
				{{-- Empty State --}}
				<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 md:p-12 text-center">
					<span class="text-4xl md:text-5xl mb-4 block">🔍</span>
					<h3 class="text-xl font-bold text-gray-800 mb-2">No products found</h3>
					<p class="text-gray-500 mb-6">Try adjusting your filters or browse all products</p>
					<a href="{{ route('catalog') }}"
						class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary text-white font-bold hover:bg-primary-dark transition">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								:d="M4 4 v5h .582 m15 .356 2 A8 .001 8.001 0 004.582 9 m0 0 H9m11 11 v - 5 h - .581 m0 0 a8 .003 8.003 0 01 -
								    15.357 - 2 m15 .357 2 H15" />
						</svg>
						Clear Filters
					</a>
				</div>
			@endif
		</div>
	</div>

	{{-- Filter Drawer Scripts --}}
	<script>
		const filterToggle = document.getElementById('mobile-filter-toggle');
		const filterDrawer = document.getElementById('filter-drawer');
		const filterOverlay = document.getElementById('filter-overlay');

		function openFilterDrawer() {
			filterDrawer.classList.remove('-translate-x-full');
			filterOverlay.classList.remove('hidden');
			document.body.style.overflow = 'hidden';
		}

		function closeFilterDrawer() {
			filterDrawer.classList.add('-translate-x-full');
			filterOverlay.classList.add('hidden');
			document.body.style.overflow = '';
		}

		if (filterToggle && filterDrawer) {
			filterToggle.addEventListener('click', openFilterDrawer);
		}

		if (filterOverlay) {
			filterOverlay.addEventListener('click', closeFilterDrawer);
		}
	</script>
@endsection
