@extends('layouts.app')

@section('title', 'Kiddo\'s Heaven — Cute & Colorful Toys')

@section('content')
	<section class="flex flex-col lg:flex-row items-center justify-between gap-10 bg-light rounded-2xl shadow-soft mb-12">
		<div class="flex-1 max-w-xl">
			<div class="flex items-center gap-2 mb-2">
				<span class="w-2 h-2 rounded-full bg-pink-300"></span>
				<span class="text-primary text-xs font-semibold uppercase tracking-wider">New arrivals just
					landed</span>
			</div>
			<h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4 ">
				Say hello to your<br>
				loved one&apos;s <span class="text-primary">new favorite</span> toys.
			</h1>
			<p class="text-lg text-primary-dark mb-6">
				A curated collection of safe, sustainable and super cuddly toys,
				designed to spark imagination and everyday joy.
			</p>
			<div class="flex gap-4 mb-6">
				<a href="{{ route('catalog') }}"
					class="inline-flex items-center px-6 py-3 rounded-lg bg-linear-to-br from-primary to-accent text-white font-bold shadow hover:from-primary-dark transition">Open
					Catalog</a>
				<a href="{{ route('checkout.show') }}"
					class="inline-flex items-center px-6 py-3 rounded-lg border border-primary text-primary-dark bg-light font-bold hover:bg-primary hover:text-white transition">Cash
					on Delivery Available</a>
			</div>
			<div class="flex gap-4">
				<div class="bg-white rounded-lg px-4 py-2 shadow text-sm flex flex-col items-start">
					<strong class="text-primary">Free delivery</strong>
					<span class="text-gray-500">on orders over $50</span>
				</div>
				<div class="bg-white rounded-lg px-4 py-2 shadow text-sm flex flex-col items-start">
					<strong class="text-primary">100% COD</strong>
					<span class="text-gray-500">Pay when it arrives</span>
				</div>
			</div>
		</div>
		<div class="flex-1 flex items-center justify-center">
			<div
				class="relative w-72 h-80 bg-linear-to-br from-accent to-primary rounded-3xl flex items-center justify-center shadow-lg">
				<div class="absolute top-4 left-4 flex items-center gap-2 bg-white/80 rounded-full px-3 py-1 text-xs shadow">
					<span class="w-2 h-2 rounded-full bg-green-200"></span>
					<span>Soft &amp; safe for tiny hands</span>
				</div>
				<div class="absolute bottom-4 right-4 flex items-center gap-2 bg-white/80 rounded-full px-3 py-1 text-xs shadow">
					<span class="text-yellow-400 text-base">⭐️⭐️⭐️⭐️⭐️</span>
					<span>People love Kiddo\'s Heaven</span>
				</div>
				<div class="absolute inset-0 flex items-center justify-center">
					<div class="w-32 h-32 bg-light rounded-full absolute top-10 left-10 blur-2xl opacity-40"></div>
					<div class="w-16 h-16 bg-primary rounded-lg absolute bottom-10 right-10 blur-lg opacity-30">
					</div>
				</div>
			</div>
		</div>
	</section>

	@foreach ($homeCatalogs as $catalog)
		<section class="mt-10" id="catalog-{{ $catalog->id }}">
			<div class="flex items-end justify-between mb-6">
				<div>
					<h2 class="text-2xl font-bold text-primary-dark">{{ $catalog->name }}</h2>
					<div class="text-gray-500 text-sm">{{ $catalog->description ?? '' }}</div>
				</div>
				<a href="{{ route('catalog', ['catalog_id' => $catalog->id]) }}" class="text-primary hover:underline font-medium">See
					all toys</a>
			</div>
			<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
				@forelse ($featuredByCatalog[$catalog->name] as $product)
					<article class="bg-white rounded-2xl shadow flex flex-col gap-2 h-full p-0">
						<a href="{{ route('products.show', $product->slug) }}">
							<div
								class="relative w-full aspect-4/3 bg-linear-to-br from-accent to-primary flex items-center justify-center rounded-t-2xl overflow-hidden">
								@php
									$img = $product->primary_image ?? ($product->images[0] ?? null);
								@endphp
								@if ($img)
									<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" loading="lazy"
										class="w-full h-full object-cover" />
								@endif
								@if ($product->is_featured)
									<div
										class="absolute top-3 left-3 bg-accent text-white text-xs px-3 py-1 rounded-full shadow font-bold tracking-wide">
										★ Featured</div>
								@endif
							</div>
						</a>
						<div class="flex flex-col gap-1 text-sm p-4">
							<div
								class="inline-block text-xs font-semibold uppercase tracking-wide px-2 py-1 rounded-full bg-light text-primary mb-1">
								{{ $catalog->name }}</div>
							<a href="{{ route('products.show', $product->slug) }}"
								class="font-semibold text-primary-dark hover:underline">{{ $product->name }}</a>
							<div class="flex items-center justify-between mt-2">
								<div class="font-bold text-primary-dark">${{ number_format($product->price / 100, 2) }} <span
										class="text-xs font-normal text-gray-400">USD</span></div>
								<form action="{{ route('cart.add', $product->slug) }}" method="post">
									@csrf
									<button type="submit"
										class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg hover:bg-primary-dark transition cursor-pointer"
										title="Add to cart">
										<!-- Cart icon (Heroicons outline) -->
										<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
											stroke="currentColor" class="w-5 h-5">
											<path stroke-linecap="round" stroke-linejoin="round"
												d="M2.25 3.75h1.5l.375 1.5m0 0l1.5 6A2.25 2.25 0 0 0 7.5 13.5h7.125a2.25 2.25 0 0 0 2.175-1.725l1.5-6m-13.5 0h13.5" />
											<path stroke-linecap="round" stroke-linejoin="round"
												d="M9 21a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm6 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
										</svg>
									</button>
								</form>
							</div>
						</div>
					</article>
				@empty
					<p class="text-gray-400">No products yet for this category. Add some products to get started.</p>
				@endforelse
			</div>
		</section>
	@endforeach

	{{-- Removed legacy hardcoded Wooden Toys section. All categories are now dynamic. --}}

	<section class="mt-12" id="about">
		<div class="container mx-auto bg-white rounded-xl shadow p-8 mt-8">
			<h1 class="text-4xl font-extrabold mb-6 text-primary-dark">About Kiddo's Heaven</h1>
			<p class="text-lg text-gray-700 mb-4">
				Welcome to Kiddo\'s Heaven, your trusted destination for delightful, safe, and sustainable toys! Founded by parents
				for
				parents, our mission is to inspire creativity, learning, and joy in every child.
			</p>
			<div class="grid md:grid-cols-2 gap-8 mb-6">
				<div>
					<h2 class="text-2xl font-bold mb-2 text-primary">Our Story</h2>
					<p class="text-gray-600 mb-2">Kiddo\'s Heaven began with a simple idea: to make playtime magical and meaningful. We
						carefully curate every toy in our collection, focusing on quality, safety, and the power of imagination.</p>
					<p class="text-gray-600">We believe in toys that last, spark curiosity, and bring families together. Our team is
						passionate about helping you find the perfect gift for every milestone and moment.</p>
				</div>
				<div>
					<h2 class="text-2xl font-bold mb-2 text-primary">Why Choose Us?</h2>
					<ul class="list-disc pl-5 text-gray-600 space-y-1">
						<li>Handpicked, high-quality toys</li>
						<li>Safe, non-toxic, and eco-friendly materials</li>
						<li>Fast, reliable shipping and cash on delivery</li>
						<li>Friendly customer support</li>
					</ul>
				</div>
			</div>
			<div class="bg-light rounded-lg p-6 mt-6">
				<h2 class="text-xl font-bold mb-2 text-primary-dark">Thank You!</h2>
				<p class="text-gray-700">We’re grateful to be part of your family’s playtime adventures. Thank you for choosing
					Kiddo\'s Heaven!</p>
			</div>
		</div>
	</section>
@endsection
