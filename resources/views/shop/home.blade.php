@extends('layouts.app')

@section('title', 'Home — Kiddo\'s Heaven')

@section('content')
	{{-- Hero Banner --}}
	<section class="mb-12">
		<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary via-primary to-primary-dark text-white">
			<div
				class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M54.627%200l.83.828-1.415%201.415L51.8%200h2.827zM0%2050.627l.828-.83-1.415-1.415L0%2054.627V50.63zM53.8%206.8l3.657-3.658L60%200v3.657L53.8%206.8zM3.658%2056.8l-3.657-3.657L0%2060h3.657L3.658%2056.8zM48.97%200l1.657%201.657-3.657%203.657L45.314%200h3.656zM11.314%2056.8l-1.657-1.657%203.657-3.657L14.97%2060h-3.656zM16%2045.314l1.657-1.657L20.314%2043H16v2.314zM43.314%2016l1.657-1.657L47.628%2014.2V16h-3.314zM6.343%2053.657L2.686%2050H6v3.657zM53.8%2053.657l-3.657-3.657%203.657-3.657L60%2050v3.657h-.2zM50.628%206.8L54.284%203.2%2057.8%200H54.2L50.628%206.8zM1.372%2053.2L-2.284%2056.8l3.657%203.657L6.343%2060H2.686L1.372%2053.2zM45.314%2060l3.657-3.657-3.657-3.657L48.97%2060h-3.656zM14.97%200l-3.657%203.657%203.657%203.657L11.314%2060h3.656L14.97%200z%22%20fill%3D%22%23fff%22%20fill-opacity%3D%22.05%22%20fill-rule%3D%22evenodd%22%2F%3E%3C%2Fsvg%3E')] opacity-50">
			</div>
			<div class="relative px-8 py-16 md:py-24 flex flex-col md:flex-row items-center justify-between gap-8">
				<div class="max-w-xl text-center md:text-left">
					<span class="inline-block px-4 py-1 rounded-full bg-white/20 text-sm font-medium mb-4">🎉 New Arrivals</span>
					<h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Where Imagination Comes to Play</h1>
					<p class="text-lg text-white/80 mb-6">Discover safe, educational, and fun toys for your little ones. Premium quality
						toys that spark creativity and joy.</p>
					<div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
						<a href="{{ route('catalog') }}"
							class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-white text-primary font-bold hover:bg-gray-100 transition shadow-lg">
							Shop Now
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
							</svg>
						</a>
						<a href="{{ route('about') }}"
							class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl border-2 border-white/50 text-white font-bold hover:bg-white/10 transition">
							Learn More
						</a>
					</div>
				</div>
				<div class="hidden md:block text-9xl filter drop-shadow-2xl">🧸</div>
			</div>
		</div>
	</section>

	{{-- Quick Links --}}
	<section class="mb-12">
		<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
			@foreach ($homeCategories as $category)
				<a href="{{ route('catalog', ['category_id' => $category->id]) }}"
					class="group bg-white rounded-xl p-6 text-center shadow-sm border border-gray-100 hover:shadow-lg hover:border-primary/30 transition-all duration-300">
					<span class="text-4xl block mb-3 group-hover:scale-110 transition">{{ $category->icon ?? '🧸' }}</span>
					<h3 class="font-bold text-gray-800 group-hover:text-primary transition">{{ $category->name }}</h3>
					<p class="text-sm text-gray-500 mt-1">Shop Now</p>
				</a>
			@endforeach
		</div>
	</section>

	{{-- Flash Sales --}}
	@if ($flashSales && $flashSales->products->isNotEmpty())
		<section class="mb-8 md:mb-12">
			<div class="bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl p-4 md:p-6 text-white mb-4">
				<div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
					<div>
						<div class="flex items-center gap-2 mb-2">
							<span class="text-2xl md:text-3xl">⚡</span>
							<h2 class="text-xl md:text-2xl font-bold">{{ $flashSales->name }}</h2>
						</div>
						<p class="text-white/90 text-sm md:text-base">{{ $flashSales->description }}</p>
					</div>
					<div class="flex items-center gap-2 bg-white/20 rounded-lg px-4 py-2">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						<span class="font-bold text-sm md:text-base">Ends {{ $flashSales->ends_at->diffForHumans() }}</span>
					</div>
				</div>
			</div>
			<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6">
				@foreach ($flashSales->products->take(8) as $product)
					<article
						class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-red-300 transition-all duration-300">
						<a href="{{ route('products.show', $product->slug) }}">
							<div class="relative aspect-4/3 bg-gray-100 overflow-hidden">
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
								<div class="absolute top-2 left-2">
									<span class="px-2 py-1 rounded-full bg-red-500 text-white text-xs font-bold shadow">
										-{{ $flashSales->discount_percentage }}%
									</span>
								</div>
							</div>
						</a>
						<div class="p-3 md:p-4">
							<a href="{{ route('products.show', $product->slug) }}"
								class="font-semibold text-sm md:text-base text-gray-800 hover:text-primary line-clamp-2">{{ $product->name }}</a>
							<div class="flex items-center gap-2 mt-2">
								<span
									class="text-base md:text-lg font-bold text-red-600">৳{{ number_format($product->price * (1 - $flashSales->discount_percentage / 100), 2) }}</span>
								<span class="text-xs md:text-sm text-gray-400 line-through">৳{{ number_format($product->price, 2) }}</span>
							</div>
						</div>
					</article>
				@endforeach
			</div>
		</section>
	@endif

	{{-- New Arrivals --}}
	@if ($newArrivals->isNotEmpty())
		<section class="mb-8 md:mb-12">
			<div class="flex items-center justify-between mb-4 md:mb-6">
				<div>
					<h2 class="text-xl md:text-2xl font-bold text-gray-900">🆕 New Arrivals</h2>
					<p class="text-gray-500 mt-1 text-sm md:text-base">Fresh toys just for you</p>
				</div>
				<a href="{{ route('catalog', ['sort' => 'newest']) }}"
					class="text-primary font-medium hover:text-primary-dark flex items-center gap-1 text-sm md:text-base">
					View All
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>
			<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6">
				@foreach ($newArrivals as $product)
					<article
						class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
						<a href="{{ route('products.show', $product->slug) }}">
							<div class="relative aspect-4/3 bg-gray-100 overflow-hidden">
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
								<div class="absolute top-2 left-2">
									<span class="px-2 py-1 rounded-full bg-green-500 text-white text-xs font-bold shadow">New</span>
								</div>
							</div>
						</a>
						<div class="p-3 md:p-4">
							@if ($product->category)
								<span
									class="inline-block px-2 py-0.5 rounded-full bg-gray-100 text-primary text-[10px] md:text-xs font-semibold mb-1 md:mb-2">{{ $product->category->name }}</span>
							@endif
							<a href="{{ route('products.show', $product->slug) }}"
								class="font-semibold text-sm md:text-base text-gray-800 hover:text-primary line-clamp-2">{{ $product->name }}</a>
							<div class="flex items-center justify-between mt-2">
								<span class="text-base md:text-lg font-bold text-primary-dark">৳{{ number_format($product->price, 2) }}</span>
								@if ($product->review_count > 0)
									<div class="flex items-center gap-1 text-yellow-400 text-xs">
										<svg class="w-3 md:w-4 h-3 md:h-4 fill-current" viewBox="0 0 20 20">
											<path
												d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
										</svg>
										<span class="text-gray-600">{{ number_format($product->average_rating, 1) }}</span>
									</div>
								@endif
							</div>
						</div>
					</article>
				@endforeach
			</div>
		</section>
	@endif

	{{-- Best Sellers --}}
	@if ($bestSellers->isNotEmpty())
		<section class="mb-8 md:mb-12">
			<div class="flex items-center justify-between mb-4 md:mb-6">
				<div>
					<h2 class="text-xl md:text-2xl font-bold text-gray-900">🔥 Best Sellers</h2>
					<p class="text-gray-500 mt-1 text-sm md:text-base">Most loved by parents</p>
				</div>
				<a href="{{ route('catalog', ['sort' => 'popular']) }}"
					class="text-primary font-medium hover:text-primary-dark flex items-center gap-1 text-sm md:text-base">
					View All
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>
			<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6">
				@foreach ($bestSellers as $product)
					<article
						class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
						<a href="{{ route('products.show', $product->slug) }}">
							<div class="relative aspect-4/3 bg-gray-100 overflow-hidden">
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
								<div class="absolute top-2 left-2">
									<span class="px-2 py-1 rounded-full bg-orange-500 text-white text-xs font-bold shadow">🔥 Hot</span>
								</div>
							</div>
						</a>
						<div class="p-3 md:p-4">
							@if ($product->category)
								<span
									class="inline-block px-2 py-0.5 rounded-full bg-gray-100 text-primary text-[10px] md:text-xs font-semibold mb-1 md:mb-2">{{ $product->category->name }}</span>
							@endif
							<a href="{{ route('products.show', $product->slug) }}"
								class="font-semibold text-sm md:text-base text-gray-800 hover:text-primary line-clamp-2">{{ $product->name }}</a>
							<div class="flex items-center justify-between mt-2">
								<span class="text-base md:text-lg font-bold text-primary-dark">৳{{ number_format($product->price, 2) }}</span>
								@if ($product->review_count > 0)
									<div class="flex items-center gap-1 text-yellow-400 text-xs">
										<svg class="w-3 md:w-4 h-3 md:h-4 fill-current" viewBox="0 0 20 20">
											<path
												d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
										</svg>
										<span class="text-gray-600">{{ number_format($product->average_rating, 1) }}</span>
									</div>
								@endif
							</div>
						</div>
					</article>
				@endforeach
			</div>
		</section>
	@endif

	{{-- Featured Products by Category --}}
	@foreach ($featuredByCategory as $categoryName => $products)
		@if ($products->isNotEmpty())
			<section class="mb-8 md:mb-12">
				<div class="flex items-center justify-between mb-4 md:mb-6">
					<div>
						<h2 class="text-xl md:text-2xl font-bold text-gray-900">{{ $categoryName }}</h2>
						<p class="text-gray-500 mt-1 text-sm md:text-base">Handpicked just for you</p>
					</div>
					@php
						$cat = $homeCategories->firstWhere('name', $categoryName);
					@endphp
					@if ($cat)
						<a href="{{ route('catalog', ['category_id' => $cat->id]) }}"
							class="text-primary font-medium hover:text-primary-dark flex items-center gap-1 text-sm md:text-base">
							View All
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
							</svg>
						</a>
					@endif
				</div>
				<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6">
					@foreach ($products as $product)
						<article
							class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
							<a href="{{ route('products.show', $product->slug) }}">
								<div class="relative aspect-4/3 bg-gray-100 overflow-hidden">
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
									class="font-semibold text-sm md:text-base text-gray-800 hover:text-primary line-clamp-2">{{ $product->name }}</a>
								<div class="flex items-center justify-between mt-2">
									<span class="text-base md:text-lg font-bold text-primary-dark">৳{{ number_format($product->price, 2) }}</span>
									@if ($product->review_count > 0)
										<div class="flex items-center gap-1 text-yellow-400 text-xs">
											<svg class="w-3 md:w-4 h-3 md:h-4 fill-current" viewBox="0 0 20 20">
												<path
													d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
											</svg>
											<span class="text-gray-600">{{ number_format($product->average_rating, 1) }}</span>
										</div>
									@endif
								</div>
							</div>
						</article>
					@endforeach
				</div>
			</section>
		@endif
	@endforeach

	{{-- All Featured Products (if no category-specific featured) --}}
	@if (empty($featuredByCategory) && $allFeatured->isNotEmpty())
		<section class="mb-8 md:mb-12">
			<div class="flex items-center justify-between mb-4 md:mb-6">
				<div>
					<h2 class="text-xl md:text-2xl font-bold text-gray-900">⭐ Featured Products</h2>
					<p class="text-gray-500 mt-1 text-sm md:text-base">Our top picks for you</p>
				</div>
				<a href="{{ route('catalog', ['featured' => '1']) }}"
					class="text-primary font-medium hover:text-primary-dark flex items-center gap-1 text-sm md:text-base">
					View All
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>
			<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6">
				@foreach ($allFeatured as $product)
					<article
						class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
						<a href="{{ route('products.show', $product->slug) }}">
							<div class="relative aspect-4/3 bg-gray-100 overflow-hidden">
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
								<div class="absolute top-2 left-2">
									<span class="px-2 py-0.5 rounded-full bg-yellow-400 text-white text-[10px] md:text-xs font-bold shadow">★
										Featured</span>
								</div>
							</div>
						</a>
						<div class="p-3 md:p-4">
							@if ($product->category)
								<span
									class="inline-block px-2 py-0.5 rounded-full bg-gray-100 text-primary text-[10px] md:text-xs font-semibold mb-1 md:mb-2">{{ $product->category->name }}</span>
							@endif
							<a href="{{ route('products.show', $product->slug) }}"
								class="font-semibold text-sm md:text-base text-gray-800 hover:text-primary line-clamp-2">{{ $product->name }}</a>
							<div class="flex items-center justify-between mt-2">
								<span class="text-base md:text-lg font-bold text-primary-dark">৳{{ number_format($product->price, 2) }}</span>
								@if ($product->review_count > 0)
									<div class="flex items-center gap-1 text-yellow-400 text-xs">
										<svg class="w-3 md:w-4 h-3 md:h-4 fill-current" viewBox="0 0 20 20">
											<path
												d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
										</svg>
										<span class="text-gray-600">{{ number_format($product->average_rating, 1) }}</span>
									</div>
								@endif
							</div>
						</div>
					</article>
				@endforeach
			</div>
		</section>
	@endif

	{{-- Newsletter --}}
	<section class="mb-12">
		<div class="bg-gradient-to-r from-primary to-primary-dark rounded-2xl p-8 md:p-12 text-center text-white">
			<div class="max-w-2xl mx-auto">
				<div class="flex items-center justify-center gap-3 mb-4">
					<div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
						</svg>
					</div>
					<h2 class="text-2xl font-bold">Stay Updated!</h2>
				</div>
				<p class="text-white/80 mb-6">Subscribe to our newsletter and be the first to know about new arrivals, exclusive
					deals, and fun activities for kids.</p>
				<form action="#" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
					<input type="email" placeholder="Enter your email"
						class="flex-1 px-4 py-3 rounded-xl text-gray-800 focus:outline-none">
					<button type="submit"
						class="px-6 py-3 rounded-xl bg-primary-dark font-bold hover:bg-gray-900 transition whitespace-nowrap">Subscribe</button>
				</form>
			</div>
		</div>
	</section>

	{{-- Trust Badges --}}
	<section class="mb-12">
		<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
			<div class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-sm border border-gray-100">
				<div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mb-3">
					<svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
					</svg>
				</div>
				<h3 class="font-bold text-gray-800">Safe & Non-Toxic</h3>
				<p class="text-sm text-gray-500 mt-1">Certified safe materials</p>
			</div>
			<div class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-sm border border-gray-100">
				<div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mb-3">
					<svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
					</svg>
				</div>
				<h3 class="font-bold text-gray-800">Free Shipping</h3>
				<p class="text-sm text-gray-500 mt-1">On orders over $50</p>
			</div>
			<div class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-sm border border-gray-100">
				<div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mb-3">
					<svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
					</svg>
				</div>
				<h3 class="font-bold text-gray-800">Easy Returns</h3>
				<p class="text-sm text-gray-500 mt-1">30-day return policy</p>
			</div>
			<div class="flex flex-col items-center text-center p-6 bg-white rounded-xl shadow-sm border border-gray-100">
				<div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mb-3">
					<svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
					</svg>
				</div>
				<h3 class="font-bold text-gray-800">24/7 Support</h3>
				<p class="text-sm text-gray-500 mt-1">Dedicated customer care</p>
			</div>
		</div>
	</section>
@endsection
