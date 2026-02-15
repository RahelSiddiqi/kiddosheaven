@extends('layouts.app')

@section('title', $product->name . ' — Kiddo\'s Heaven')


@section('content')
	{{-- Breadcrumb --}}
	<nav class="mb-4 md:mb-6 text-sm">
		<ol class="flex items-center gap-1 md:gap-2 text-gray-500 overflow-x-auto whitespace-nowrap pb-2">
			<li><a href="{{ route('home') }}" class="hover:text-[var(--color-primary)] transition">Home</a></li>
			<li><svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
				</svg></li>
			<li><a href="{{ route('catalog') }}" class="hover:text-[var(--color-primary)] transition">Shop</a></li>
			@if ($product->category)
				<li><svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg></li>
				<li><a href="{{ route('catalog', ['category_id' => $product->category->id]) }}"
						class="hover:text-[var(--color-primary)] transition">{{ $product->category->name }}</a>
				</li>
			@endif
			<li><svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
				</svg></li>
			<li class="text-gray-800 font-medium truncate max-w-[120px] md:max-w-[200px]">{{ $product->name }}</li>
		</ol>
	</nav>

	<div class="grid md:grid-cols-2 gap-6 md:gap-8 mb-24 md:mb-12">
		{{-- Product Gallery --}}
		<div class="space-y-4">
			{{-- Main Image --}}
			<div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
				@php
					$mainImage = $product->primary_image ?? ($product->images[0] ?? null);
				@endphp
				<div class="relative aspect-[4/3] bg-gray-50">
					<img id="mainProductImage" src="{{ $mainImage ? asset($mainImage) : '' }}" alt="{{ $product->name }}" loading="lazy"
						class="w-full h-full object-contain {{ $mainImage ? '' : 'hidden' }} transition-transform duration-300"
						style="touch-action: pan-x;">
					@if (!$mainImage)
						<div class="w-full h-full flex items-center justify-center">
							<span class="text-6xl">🧸</span>
						</div>
					@endif
					{{-- Badges --}}
					<div class="absolute top-4 left-4 flex flex-col gap-2">
						@if ($product->is_featured)
							<span class="px-3 py-1 rounded-full bg-yellow-400 text-white text-sm font-bold shadow">★ Featured</span>
						@endif
						@if ($product->created_at && $product->created_at->diffInDays() < 14)
							<span class="px-3 py-1 rounded-full bg-green-500 text-white text-sm font-bold shadow">New Arrival</span>
						@endif
					</div>
					{{-- Actions --}}
					<div class="absolute top-4 right-4 flex flex-col gap-2">
						<button
							class="w-10 h-10 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-50 transition"
							title="Add to wishlist">
							<svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
							</svg>
						</button>
						<button
							class="w-10 h-10 rounded-full bg-white shadow-lg flex items-center justify-center hover:bg-gray-50 transition"
							title="Share">
							<svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
							</svg>
						</button>
					</div>
				</div>
			</div>

			{{-- Thumbnail Gallery --}}
			@php
				$productImages = is_array($product->images) ? $product->images : [];
			@endphp
			@if (count($productImages) > 1)
				<div class="flex gap-3 overflow-x-auto pb-2" id="product-thumbnails">
					@foreach ($productImages as $img)
						<button onclick="document.getElementById('mainProductImage').src = '{{ asset($img) }}'"
							class="w-20 h-20 rounded-lg overflow-hidden border-2 border-transparent hover:border-[var(--color-primary)] transition flex-shrink-0">
							<img src="{{ asset($img) }}" alt="Thumbnail" class="w-full h-full object-cover">
						</button>
					@endforeach
				</div>
				<script>
					// Swipe to change main image on mobile
					const mainImg = document.getElementById('mainProductImage');
					let startX = 0,
						current = 0;
					mainImg.addEventListener('touchstart', e => {
						startX = e.touches[0].clientX;
					});
					mainImg.addEventListener('touchend', e => {
						let dx = e.changedTouches[0].clientX - startX;
						if (Math.abs(dx) > 40) {
							const thumbs = Array.from(document.querySelectorAll('#product-thumbnails button img'));
							let idx = thumbs.findIndex(img => img.src === mainImg.src);
							if (dx < 0 && idx < thumbs.length - 1) mainImg.src = thumbs[idx + 1].src;
							if (dx > 0 && idx > 0) mainImg.src = thumbs[idx - 1].src;
						}
					});
				</script>
			@endif
		</div>

		{{-- Product Info --}}
		<div class="space-y-6">
			{{-- Category & Rating --}}
			<div class="flex items-center justify-between">
				@if ($product->category)
					<a href="{{ route('catalog', ['category_id' => $product->category->id]) }}"
						class="inline-flex items-center gap-1 text-primary hover:text-primary-dark font-medium transition">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
						</svg>
						{{ $product->category->name }}
					</a>
				@endif
				<div class="flex items-center gap-2">
					@php
						$avgRating = $product->average_rating;
						$reviewCount = $product->review_count;
						$fullStars = floor($avgRating);
						$hasHalfStar = $avgRating - $fullStars >= 0.5;
					@endphp
					<div class="flex items-center text-yellow-400">
						@for ($i = 1; $i <= 5; $i++)
							@if ($i <= $fullStars)
								<svg class="w-4 md:w-5 h-4 md:h-5 fill-current" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
							@elseif ($i == $fullStars + 1 && $hasHalfStar)
								<svg class="w-4 md:w-5 h-4 md:h-5 fill-current" viewBox="0 0 20 20">
									<defs>
										<linearGradient id="half-star">
											<stop offset="50%" stop-color="currentColor" />
											<stop offset="50%" stop-color="#d1d5db" />
										</linearGradient>
									</defs>
									<path fill="url(#half-star)"
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
							@else
								<svg class="w-4 md:w-5 h-4 md:h-5 fill-current text-gray-300" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
							@endif
						@endfor
					</div>
					<span class="text-xs md:text-sm text-gray-500">
						@if ($reviewCount > 0)
							({{ number_format($avgRating, 1) }} • {{ $reviewCount }} {{ $reviewCount == 1 ? 'review' : 'reviews' }})
						@else
							(No reviews yet)
						@endif
					</span>
				</div>
			</div>

			{{-- Title --}}
			<h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

			{{-- Short Description --}}
			@if ($product->short_description)
				<p class="text-gray-600 text-lg leading-relaxed">{{ $product->short_description }}</p>
			@endif

			{{-- Price --}}
			<div class="flex items-center gap-2 md:gap-4 flex-wrap">
				<span class="text-2xl md:text-4xl font-bold text-primary-dark">৳{{ number_format($product->price, 2) }}</span>
				@if ($product->compare_at_price)
					<span class="text-xl text-gray-400 line-through">৳{{ number_format($product->compare_at_price, 2) }}</span>
					<span class="px-2 py-1 rounded-full bg-red-100 text-red-600 text-sm font-bold">Save
						{{ round((1 - $product->price / $product->compare_at_price) * 100) }}%</span>
				@endif
			</div>

			{{-- Stock Status --}}
			@if ($product->stock_quantity > 0)
				<div class="flex items-center gap-2 mt-2">
					<span class="w-2 h-2 rounded-full bg-green-500"></span>
					<span class="text-sm text-green-600 font-medium">
						@if ($product->stock_quantity <= 5)
							Only {{ $product->stock_quantity }} left in stock
						@else
							In Stock ({{ $product->stock_quantity }} available)
						@endif
					</span>
				</div>
			@else
				<div class="flex items-center gap-2 mt-2">
					<span class="w-2 h-2 rounded-full bg-red-500"></span>
					<span class="text-sm text-red-600 font-medium">Out of Stock</span>
				</div>
			@endif

			{{-- Description --}}
			@if ($product->description)
				<div class="prose prose-sm max-w-none">
					<p class="text-gray-600">{{ $product->description }}</p>
				</div>
			@endif

			{{-- Product Variants (if variable product) --}}
			@if ($product->product_type === 'variable' && $variants->isNotEmpty())
				<div class="bg-gray-50 rounded-xl p-4 md:p-6">
					<h3 class="font-semibold text-gray-900 mb-4 text-sm md:text-base">Choose Option</h3>
					<div class="space-y-3">
						@foreach ($variants as $variant)
							<label
								class="flex items-center justify-between p-3 md:p-4 bg-white rounded-lg border-2 border-gray-200 hover:border-primary cursor-pointer transition">
								<div class="flex items-center gap-3 flex-1 min-w-0">
									<input type="radio" name="variant_id" value="{{ $variant->id }}"
										class="w-5 h-5 text-primary focus:ring-primary" {{ $variant->is_default ? 'checked' : '' }}>
									<div class="flex-1 min-w-0">
										<p class="font-medium text-gray-800 text-sm md:text-base">{{ $variant->name }}</p>
										@if ($variant->variantAttributes->isNotEmpty())
											<p class="text-xs md:text-sm text-gray-500">
												{{ $variant->variantAttributes->map(fn($va) => $va->attributeValue->value ?? '')->implode(' • ') }}
											</p>
										@endif
										@if (!$variant->is_in_stock)
											<span class="text-xs text-red-600 font-medium">Out of Stock</span>
										@elseif ($variant->is_low_stock)
											<span class="text-xs text-orange-600 font-medium">Only {{ $variant->available_quantity }} left</span>
										@endif
									</div>
								</div>
								<span
									class="font-bold text-primary-dark text-sm md:text-base whitespace-nowrap ml-2">৳{{ number_format($variant->price, 2) }}</span>
							</label>
						@endforeach
					</div>
				</div>
			@endif

			{{-- Add to Cart --}}
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6" id="add-to-cart-section">
				<div class="flex flex-col sm:flex-row gap-3 md:gap-4">
					<form action="{{ route('cart.add', $product->slug) }}" method="post" class="flex-1">
						@csrf
						<div class="flex items-center justify-between md:justify-start gap-3 md:gap-4 mb-3 md:mb-4">
							<label class="text-sm font-medium text-gray-700">Quantity:</label>
							<div class="flex items-center border border-gray-200 rounded-lg">
								<button type="button" onclick="this.parentElement.querySelector('input').stepDown()"
									class="w-10 md:w-12 h-10 md:h-12 flex items-center justify-center hover:bg-gray-100 text-gray-600 transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
									</svg>
								</button>
								<input type="number" name="quantity" value="1" min="1" max="99"
									class="w-12 md:w-16 h-10 md:h-12 text-center border-x border-gray-200 focus:outline-none">
								<button type="button" onclick="this.parentElement.querySelector('input').stepUp()"
									class="w-10 md:w-12 h-10 md:h-12 flex items-center justify-center hover:bg-gray-100 text-gray-600 transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
									</svg>
								</button>
							</div>
						</div>
						<button type="submit"
							class="w-full flex items-center justify-center gap-2 px-4 md:px-6 py-3 md:py-4 rounded-xl bg-[var(--color-primary)] text-white font-bold text-base md:text-lg hover:bg-[var(--color-primary-dark)] transition shadow-lg shadow-primary/30 min-h-[52px]">
							<svg class="w-5 md:w-6 h-5 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
							Add to Cart
						</button>
					</form>
					<a href="{{ route('checkout.show') }}"
						class="flex-1 flex items-center justify-center gap-2 px-4 md:px-6 py-3 md:py-4 rounded-xl border-2 border-[var(--color-primary)] text-[var(--color-primary)] font-bold text-base md:text-lg hover:bg-[var(--color-primary)] hover:text-white transition min-h-[52px]">
						Buy Now
					</a>
				</div>

				{{-- Sticky Action Bar for Mobile --}}
				<div
					class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-lg flex md:hidden px-4 py-3 gap-3"
					style="backdrop-filter: blur(8px);">
					<form action="{{ route('cart.add', $product->slug) }}" method="post" class="flex-1">
						@csrf
						<input type="hidden" name="quantity" value="1">
						<button type="submit"
							class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-[var(--color-primary)] text-white font-bold text-base hover:bg-[var(--color-primary-dark)] transition min-h-[48px]">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
							Add to Cart
						</button>
					</form>
					<a href="{{ route('checkout.show') }}"
						class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-[var(--color-primary)] text-[var(--color-primary)] font-bold text-base hover:bg-[var(--color-primary)] hover:text-white transition min-h-[48px]">
						Buy Now
					</a>
				</div>

				{{-- Trust Badges --}}
				<div
					class="flex flex-wrap items-center justify-center gap-3 md:gap-6 mt-4 md:mt-6 pt-4 md:pt-6 border-t border-gray-100">
					<div class="flex items-center gap-2 text-sm text-gray-600">
						<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
						</svg>
						<span>Free Delivery $50+</span>
					</div>
					<div class="flex items-center gap-2 text-sm text-gray-600">
						<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
						</svg>
						<span>Cash on Delivery</span>
					</div>
					<div class="flex items-center gap-2 text-sm text-gray-600">
						<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
						</svg>
						<span>Safe & Non-Toxic</span>
					</div>
				</div>
			</div>

			{{-- Product Details Sections --}}
			<div class="space-y-6">
				{{-- Brand & SKU --}}
				@if ($product->brand || $product->sku)
					<div class="bg-gray-50 rounded-xl p-4">
						<div class="grid grid-cols-2 gap-4 text-sm">
							@if ($product->brand)
								<div>
									<span class="text-gray-500">Brand</span>
									<p class="font-medium text-gray-900">{{ $product->brand->name }}</p>
								</div>
							@endif
							@if ($product->sku)
								<div>
									<span class="text-gray-500">SKU</span>
									<p class="font-medium text-gray-900">{{ $product->sku }}</p>
								</div>
							@endif
							@if ($product->manufacturer)
								<div>
									<span class="text-gray-500">Manufacturer</span>
									<p class="font-medium text-gray-900">{{ $product->manufacturer }}</p>
								</div>
							@endif
							@if ($product->tags && is_array($product->tags) && count($product->tags) > 0)
								<div class="col-span-2">
									<span class="text-gray-500">Tags</span>
									<div class="flex flex-wrap gap-2 mt-1">
										@foreach ($product->tags as $tag)
											<span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $tag }}</span>
										@endforeach
									</div>
								</div>
							@endif
						</div>
					</div>
				@endif

				{{-- Product Features from Database --}}
				@if ($product->features)
					<div class="bg-light rounded-xl p-6">
						<h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
							<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
							</svg>
							Key Features
						</h3>
						<div class="prose prose-sm max-w-none text-gray-600">
							{!! nl2br(e($product->features)) !!}
						</div>
					</div>
				@endif

				{{-- Certifications --}}
				@if ($product->halal_certified || $product->organic_certified)
					<div class="flex flex-wrap gap-3">
						@if ($product->halal_certified)
							<span
								class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-sm font-medium">
								<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd"
										d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
										clip-rule="evenodd" />
								</svg>
								Halal Certified
							</span>
						@endif
						@if ($product->organic_certified)
							<span
								class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-sm font-medium">
								<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd"
										d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
										clip-rule="evenodd" />
								</svg>
								Organic Certified
							</span>
						@endif
					</div>
				@endif

				{{-- Care Instructions --}}
				@if ($product->care_instructions)
					<div class="bg-gray-50 rounded-xl p-6">
						<h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
							<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
							</svg>
							Care Instructions
						</h3>
						<p class="text-sm text-gray-600">{!! nl2br(e($product->care_instructions)) !!}</p>
					</div>
				@endif

				{{-- Safety Warning --}}
				@if ($product->safety_warning)
					<div class="bg-red-50 rounded-xl p-4 border border-red-100">
						<h3 class="font-bold text-red-700 mb-2 flex items-center gap-2">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
							</svg>
							Safety Warning
						</h3>
						<p class="text-sm text-red-600">{!! nl2br(e($product->safety_warning)) !!}</p>
					</div>
				@endif

				{{-- Return Policy & Warranty --}}
				@if ($product->return_policy || $product->warranty)
					<div class="bg-gray-50 rounded-xl p-6">
						<h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
							<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
							Warranty & Returns
						</h3>
						<div class="space-y-2 text-sm text-gray-600">
							@if ($product->warranty)
								<p><span class="font-medium">Warranty:</span> {!! nl2br(e($product->warranty)) !!}</p>
							@endif
							@if ($product->return_policy)
								<p><span class="font-medium">Return Policy:</span> {!! nl2br(e($product->return_policy)) !!}</p>
							@endif
						</div>
					</div>
				@endif

				{{-- Product Video --}}
				@if ($product->video_url)
					<div class="bg-gray-50 rounded-xl p-6">
						<h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
							<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
							Product Video
						</h3>
						<div class="aspect-video rounded-lg overflow-hidden bg-gray-100">
							@php
								$videoUrl = $product->video_url;
								$embedUrl = null;
								if (
								    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $matches)
								) {
								    $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
								}
							@endphp
							@if ($embedUrl)
								<iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allowfullscreen
									allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
							@else
								<video controls class="w-full h-full object-contain">
									<source src="{{ $videoUrl }}" type="video/mp4">
									Your browser does not support the video tag.
								</video>
							@endif
						</div>
					</div>
				@endif

				{{-- Ingredients --}}
				@if ($product->ingredients)
					<div class="bg-gray-50 rounded-xl p-6">
						<h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
							<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
							</svg>
							Ingredients
						</h3>
						<p class="text-sm text-gray-600">{!! nl2br(e($product->ingredients)) !!}</p>
					</div>
				@endif

				{{-- Weight & Dimensions --}}
				@if ($product->weight || $product->length || $product->width || $product->height)
					<div class="bg-gray-50 rounded-xl p-6">
						<h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
							<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
							</svg>
							Specifications
						</h3>
						<div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
							@if ($product->weight)
								<div>
									<span class="text-gray-500">Weight</span>
									<p class="font-medium text-gray-900">{{ $product->weight }} g</p>
								</div>
							@endif
							@if ($product->length)
								<div>
									<span class="text-gray-500">Length</span>
									<p class="font-medium text-gray-900">{{ $product->length }} cm</p>
								</div>
							@endif
							@if ($product->width)
								<div>
									<span class="text-gray-500">Width</span>
									<p class="font-medium text-gray-900">{{ $product->width }} cm</p>
								</div>
							@endif
							@if ($product->height)
								<div>
									<span class="text-gray-500">Height</span>
									<p class="font-medium text-gray-900">{{ $product->height }} cm</p>
								</div>
							@endif
						</div>
					</div>
				@endif

				{{-- Sold Count & Delivery Type --}}
				@if ($product->sold_count || $product->delivery_type)
					<div class="flex flex-wrap gap-4 text-sm">
						@if ($product->sold_count)
							<span class="text-gray-500">
								<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
								</svg>
								{{ $product->sold_count }} sold
							</span>
						@endif
						@if ($product->delivery_type)
							<span class="text-gray-500">
								<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
								</svg>
								{{ ucfirst($product->delivery_type) }} Delivery
							</span>
						@endif
					</div>
				@endif
			</div>
		</div>
	</div>

	{{-- Related Products --}}
	@if ($related->isNotEmpty())
		<section class="mb-12">
			<div class="flex items-center justify-between mb-6">
				<h2 class="text-2xl font-bold text-gray-900">You May Also Like</h2>
				<a href="{{ route('catalog') }}" class="text-primary font-medium hover:text-primary-dark flex items-center gap-1">
					View All
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>
			<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
				@foreach ($related as $item)
					<article
						class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
						<a href="{{ route('products.show', $item->slug) }}">
							<div class="relative aspect-4/3 bg-gray-100 overflow-hidden">
								@php
									$relatedImages = is_array($item->images) ? $item->images : [];
									$img = $item->primary_image ?? ($relatedImages[0] ?? null);
								@endphp
								@if ($img)
									<img src="{{ asset($img) }}" alt="{{ $item->name }}" loading="lazy"
										class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
								@else
									<div class="w-full h-full flex items-center justify-center">
										<span class="text-4xl">🧸</span>
									</div>
								@endif
							</div>
						</a>
						<div class="p-4">
							@if ($item->category)
								<span
									class="inline-block px-2 py-1 rounded-full bg-light text-primary text-xs font-semibold mb-1">{{ $item->category->name }}</span>
							@endif
							<a href="{{ route('products.show', $item->slug) }}"
								class="font-semibold text-gray-800 hover:text-primary line-clamp-2 text-sm">{{ $item->name }}</a>
							<div class="flex items-center justify-between mt-2">
								<span class="font-bold text-primary-dark">৳{{ number_format($item->price, 2) }}</span>
								<form action="{{ route('cart.add', $item->slug) }}" method="post">
									@csrf
									<button type="submit"
										class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center hover:bg-primary-dark transition">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
										</svg>
									</button>
								</form>
							</div>
						</div>
					</article>
				@endforeach
			</div>
		</section>
	@endif

	{{-- Customer Reviews Section --}}
	<section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-8">
		<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8">
			<div>
				<h2 class="text-xl md:text-2xl font-bold text-gray-900">Customer Reviews</h2>
				<p class="text-gray-500 mt-1 text-sm md:text-base">See what parents are saying</p>
			</div>
			@auth
				<button
					class="px-4 py-2.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition text-sm md:text-base min-h-[48px]">
					Write a Review
				</button>
			@endauth
		</div>

		@if ($product->review_count > 0)
			{{-- Rating Summary --}}
			<div
				class="flex flex-col md:flex-row md:items-center gap-6 md:gap-8 mb-6 md:mb-8 pb-6 md:pb-8 border-b border-gray-100">
				<div class="text-center md:text-left">
					<span
						class="text-4xl md:text-5xl font-bold text-primary-dark">{{ number_format($product->average_rating, 1) }}</span>
					<div class="flex justify-center text-yellow-400 my-2">
						<svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
						<svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
						<svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
						<svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
						<svg class="w-6 h-6 fill-current text-gray-300" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
					</div>
					<p class="text-xs md:text-sm text-gray-500">{{ $product->review_count }}
						{{ $product->review_count == 1 ? 'review' : 'reviews' }}</p>
				</div>
			</div>

			{{-- Reviews List --}}
			<div class="space-y-4 md:space-y-6">
				@forelse ($reviews as $review)
					<div class="border-b border-gray-100 pb-4 md:pb-6 last:border-0">
						<div class="flex items-start justify-between mb-2 gap-2">
							<div class="flex items-start gap-2 md:gap-3 flex-1 min-w-0">
								<div class="w-8 md:w-10 h-8 md:h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
									<span
										class="text-primary font-bold text-sm md:text-base">{{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}</span>
								</div>
								<div class="flex-1 min-w-0">
									<p class="font-medium text-gray-800 text-sm md:text-base truncate">{{ $review->user->name ?? 'Anonymous' }}
									</p>
									<div class="flex text-yellow-400 text-sm mt-1">
										@for ($i = 1; $i <= 5; $i++)
											<svg class="w-3 md:w-4 h-3 md:h-4 fill-current {{ $i <= $review->rating ? '' : 'text-gray-300' }}"
												viewBox="0 0 20 20">
												<path
													d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
											</svg>
										@endfor
									</div>
									@if ($review->is_verified_purchase)
										<span class="inline-flex items-center gap-1 text-xs text-green-600 mt-1">
											<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
											</svg>
											Verified Purchase
										</span>
									@endif
								</div>
							</div>
							<span
								class="text-xs md:text-sm text-gray-400 whitespace-nowrap">{{ $review->created_at->diffForHumans() }}</span>
						</div>
						@if ($review->title)
							<h4 class="font-semibold text-gray-800 mb-1 text-sm md:text-base">{{ $review->title }}</h4>
						@endif
						<p class="text-gray-600 text-sm md:text-base">{{ $review->content }}</p>
					</div>
				@empty
					<div class="text-center py-8">
						<span class="text-4xl md:text-5xl mb-3 block">📝</span>
						<p class="text-gray-500 text-sm md:text-base">No reviews yet. Be the first to review this product!</p>
					</div>
				@endforelse
			</div>
		@else
			<div class="text-center py-8">
				<span class="text-4xl md:text-5xl mb-3 block">📝</span>
				<p class="text-gray-500 text-sm md:text-base">No reviews yet. Be the first to review this product!</p>
			</div>
		@endif
	</section>
@endsection
