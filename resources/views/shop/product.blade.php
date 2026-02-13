@extends('layouts.app')

@section('title', $product->name . ' — Kiddo\'s Heaven')

@section('content')
	{{-- Breadcrumb --}}
	<nav class="mb-6 text-sm">
		<ol class="flex items-center gap-2 text-gray-500">
			<li><a href="{{ route('home') }}" class="hover:text-primary transition">Home</a></li>
			<li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
				</svg></li>
			<li><a href="{{ route('catalog') }}" class="hover:text-primary transition">Shop</a></li>
			@if ($product->category)
				<li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg></li>
				<li><a href="{{ route('catalog', ['category_id' => $product->category->id]) }}"
						class="hover:text-primary transition">{{ $product->category->name }}</a>
				</li>
			@endif
			<li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
				</svg></li>
			<li class="text-gray-800 font-medium truncate max-w-[200px]">{{ $product->name }}</li>
		</ol>
	</nav>

	<div class="grid lg:grid-cols-2 gap-8 mb-12">
		{{-- Product Gallery --}}
		<div class="space-y-4">
			{{-- Main Image --}}
			<div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
				@php
					$mainImage = $product->primary_image ?? ($product->images[0] ?? null);
				@endphp
				<div class="relative aspect-[4/3] bg-gray-50">
					<img id="mainProductImage" src="{{ $mainImage ? asset('storage/' . $mainImage) : '' }}" alt="{{ $product->name }}"
						loading="lazy" class="w-full h-full object-contain {{ $mainImage ? '' : 'hidden' }}">
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
			@if (!empty($product->images) && count($product->images) > 1)
				<div class="flex gap-3 overflow-x-auto pb-2">
					@foreach ($product->images as $img)
						<button onclick="document.getElementById('mainProductImage').src = '{{ asset('storage/' . $img) }}'"
							class="w-20 h-20 rounded-lg overflow-hidden border-2 border-transparent hover:border-primary transition flex-shrink-0">
							<img src="{{ asset('storage/' . $img) }}" alt="Thumbnail" class="w-full h-full object-cover">
						</button>
					@endforeach
				</div>
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
					<div class="flex items-center text-yellow-400">
						<svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
						<svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
						<svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
						<svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
						<svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
					</div>
					<span class="text-sm text-gray-500">(4.5 • 128 reviews)</span>
				</div>
			</div>

			{{-- Title --}}
			<h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

			{{-- Short Description --}}
			@if ($product->short_description)
				<p class="text-gray-600 text-lg leading-relaxed">{{ $product->short_description }}</p>
			@endif

			{{-- Price --}}
			<div class="flex items-center gap-4">
				<span class="text-4xl font-bold text-primary-dark">৳{{ number_format($product->price, 2) }}</span>
				@if ($product->compare_price)
					<span class="text-xl text-gray-400 line-through">৳{{ number_format($product->compare_price, 2) }}</span>
					<span class="px-2 py-1 rounded-full bg-red-100 text-red-600 text-sm font-bold">Save
						{{ round((1 - $product->price / $product->compare_price) * 100) }}%</span>
				@endif
			</div>

			{{-- Description --}}
			@if ($product->description)
				<div class="prose prose-sm max-w-none">
					<p class="text-gray-600">{{ $product->description }}</p>
				</div>
			@endif

			{{-- Add to Cart --}}
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<div class="flex flex-col sm:flex-row gap-4">
					<form action="{{ route('cart.add', $product->slug) }}" method="post" class="flex-1">
						@csrf
						<div class="flex items-center gap-4 mb-4">
							<label class="text-sm font-medium text-gray-700">Quantity:</label>
							<div class="flex items-center border border-gray-200 rounded-lg">
								<button type="button" onclick="this.parentElement.querySelector('input').stepDown()"
									class="px-4 py-2 hover:bg-gray-100 text-gray-600 transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
									</svg>
								</button>
								<input type="number" name="quantity" value="1" min="1" max="99"
									class="w-16 text-center border-x border-gray-200 py-2 focus:outline-none">
								<button type="button" onclick="this.parentElement.querySelector('input').stepUp()"
									class="px-4 py-2 hover:bg-gray-100 text-gray-600 transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
									</svg>
								</button>
							</div>
						</div>
						<button type="submit"
							class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition shadow-lg shadow-primary/30">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
							Add to Cart
						</button>
					</form>
					<a href="{{ route('checkout.show') }}"
						class="flex-1 flex items-center justify-center gap-2 px-6 py-4 rounded-xl border-2 border-primary text-primary font-bold text-lg hover:bg-primary hover:text-white transition">
						Buy Now
					</a>
				</div>

				{{-- Trust Badges --}}
				<div class="flex flex-wrap items-center justify-center gap-6 mt-6 pt-6 border-t border-gray-100">
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

			{{-- Product Features --}}
			<div class="bg-light rounded-xl p-6">
				<h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
					<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
					</svg>
					Product Features
				</h3>
				<ul class="grid sm:grid-cols-2 gap-3 text-sm text-gray-600">
					<li class="flex items-center gap-2">
						<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
						</svg>
						Non-toxic materials
					</li>
					<li class="flex items-center gap-2">
						<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
						</svg>
						Age appropriate
					</li>
					<li class="flex items-center gap-2">
						<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
						</svg>
						Durable construction
					</li>
					<li class="flex items-center gap-2">
						<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
						</svg>
						Easy to clean
					</li>
				</ul>
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
									$img = $item->primary_image ?? ($item->images[0] ?? null);
								@endphp
								@if ($img)
									<img src="{{ asset('storage/' . $img) }}" alt="{{ $item->name }}" loading="lazy"
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
	<section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
		<div class="flex items-center justify-between mb-8">
			<div>
				<h2 class="text-2xl font-bold text-gray-900">Customer Reviews</h2>
				<p class="text-gray-500 mt-1">See what parents are saying</p>
			</div>
			<button class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
				Write a Review
			</button>
		</div>

		{{-- Rating Summary --}}
		<div class="flex items-center gap-8 mb-8 pb-8 border-b border-gray-100">
			<div class="text-center">
				<span class="text-5xl font-bold text-primary-dark">4.5</span>
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
				<p class="text-sm text-gray-500">128 reviews</p>
			</div>
			<div class="flex-1 space-y-2">
				<div class="flex items-center gap-2">
					<span class="text-sm text-gray-500 w-12">5 ★</span>
					<div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
						<div class="h-full bg-yellow-400 rounded-full" style="width: 70%"></div>
					</div>
					<span class="text-sm text-gray-500 w-12">70%</span>
				</div>
				<div class="flex items-center gap-2">
					<span class="text-sm text-gray-500 w-12">4 ★</span>
					<div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
						<div class="h-full bg-yellow-400 rounded-full" style="width: 20%"></div>
					</div>
					<span class="text-sm text-gray-500 w-12">20%</span>
				</div>
				<div class="flex items-center gap-2">
					<span class="text-sm text-gray-500 w-12">3 ★</span>
					<div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
						<div class="h-full bg-yellow-400 rounded-full" style="width: 7%"></div>
					</div>
					<span class="text-sm text-gray-500 w-12">7%</span>
				</div>
				<div class="flex items-center gap-2">
					<span class="text-sm text-gray-500 w-12">2 ★</span>
					<div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
						<div class="h-full bg-yellow-400 rounded-full" style="width: 2%"></div>
					</div>
					<span class="text-sm text-gray-500 w-12">2%</span>
				</div>
				<div class="flex items-center gap-2">
					<span class="text-sm text-gray-500 w-12">1 ★</span>
					<div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
						<div class="h-full bg-yellow-400 rounded-full" style="width: 1%"></div>
					</div>
					<span class="text-sm text-gray-500 w-12">1%</span>
				</div>
			</div>
		</div>

		{{-- Sample Reviews --}}
		<div class="space-y-6">
			{{-- Review 1 --}}
			<div class="border-b border-gray-100 pb-6">
				<div class="flex items-start justify-between mb-2">
					<div class="flex items-center gap-3">
						<div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
							<span class="text-primary font-bold">S</span>
						</div>
						<div>
							<p class="font-medium text-gray-800">Sarah M.</p>
							<div class="flex text-yellow-400 text-sm">
								<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
								<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
								<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
								<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
								<svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
							</div>
						</div>
					</div>
					<span class="text-sm text-gray-400">2 weeks ago</span>
				</div>
				<p class="text-gray-600">My daughter absolutely loves this toy! The quality is excellent and it's so soft and
					cuddly. Highly recommend!</p>
			</div>
		</div>
	</section>
@endsection
