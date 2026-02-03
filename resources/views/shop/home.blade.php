@extends('layouts.app')

@section('title', 'KiddosHeaven — Cute & Colorful Toys')

@section('content')
	<section
		class="flex flex-col lg:flex-row items-center justify-between gap-10 py-12 px-4 bg-[color:var(--color-light)] rounded-2xl shadow-[var(--kh-shadow-soft)] mb-12">
		<div class="flex-1 max-w-xl">
			<div class="flex items-center gap-2 mb-2">
				<span class="w-2 h-2 rounded-full bg-pink-300"></span>
				<span class="text-[color:var(--color-primary)] text-xs font-semibold uppercase tracking-wider">New arrivals just
					landed</span>
			</div>
			<h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-4">
				Say hello to your<br>
				child&apos;s <span class="text-[color:var(--color-primary)]">new favorite</span> toys.
			</h1>
			<p class="text-lg text-[color:var(--color-primary-dark)] mb-6">
				A curated collection of safe, sustainable and super cuddly toys,
				designed to spark imagination and everyday joy.
			</p>
			<div class="flex gap-4 mb-6">
				<a href="{{ route('catalog') }}"
					class="inline-flex items-center px-6 py-3 rounded-lg bg-gradient-to-br from-[color:var(--color-primary)] to-[color:var(--color-accent)] text-white font-bold shadow hover:from-[color:var(--color-primary-dark)] transition">Open
					Catalog</a>
				<a href="{{ route('checkout.show') }}"
					class="inline-flex items-center px-6 py-3 rounded-lg border border-[color:var(--color-primary)] text-[color:var(--color-primary-dark)] bg-[color:var(--color-light)] font-bold hover:bg-[color:var(--color-primary)] hover:text-white transition">Cash
					on Delivery Available</a>
			</div>
			<div class="flex gap-4">
				<div class="bg-white rounded-lg px-4 py-2 shadow text-sm flex flex-col items-start">
					<strong class="text-[color:var(--color-primary)]">Free delivery</strong>
					<span class="text-gray-500">on orders over $50</span>
				</div>
				<div class="bg-white rounded-lg px-4 py-2 shadow text-sm flex flex-col items-start">
					<strong class="text-[color:var(--color-primary)]">100% COD</strong>
					<span class="text-gray-500">Pay when it arrives</span>
				</div>
			</div>
		</div>
		<div class="flex-1 flex items-center justify-center">
			<div
				class="relative w-72 h-80 bg-gradient-to-br from-[color:var(--color-accent)] to-[color:var(--color-primary)] rounded-3xl flex items-center justify-center shadow-lg">
				<div class="absolute top-4 left-4 flex items-center gap-2 bg-white/80 rounded-full px-3 py-1 text-xs shadow">
					<span class="w-2 h-2 rounded-full bg-green-200"></span>
					<span>Soft &amp; safe for tiny hands</span>
				</div>
				<div class="absolute bottom-4 right-4 flex items-center gap-2 bg-white/80 rounded-full px-3 py-1 text-xs shadow">
					<span class="text-yellow-400 text-base">⭐️⭐️⭐️⭐️⭐️</span>
					<span>Parents love KiddosHeaven</span>
				</div>
				<div class="absolute inset-0 flex items-center justify-center">
					<div class="w-32 h-32 bg-[color:var(--color-light)] rounded-full absolute top-10 left-10 blur-2xl opacity-40"></div>
					<div class="w-16 h-16 bg-[color:var(--color-primary)] rounded-lg absolute bottom-10 right-10 blur-lg opacity-30">
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="mt-10" id="stuffed">
		<div class="flex items-end justify-between mb-6">
			<div>
				<h2 class="text-2xl font-bold text-[color:var(--color-primary-dark)]">Stuffed Animals</h2>
				<div class="text-gray-500 text-sm">Soft friends for snuggles and story time.</div>
			</div>
			<a href="{{ route('catalog', ['category' => 'Stuffed Animals']) }}"
				class="text-[color:var(--color-primary)] hover:underline font-medium">See all toys</a>
		</div>
		<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
			@forelse ($featuredStuffed as $product)
				<article class="bg-white rounded-2xl shadow flex flex-col gap-2 h-full p-0">
					<a href="{{ route('products.show', $product->slug) }}">
						<div
							class="relative w-full aspect-[4/3] bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-primary)] flex items-center justify-center rounded-t-2xl overflow-hidden">
							@php
								$img = $product->primary_image ?? ($product->images[0] ?? null);
							@endphp
							@if ($img)
								<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" loading="lazy"
									class="w-full h-full object-cover" />
							@endif
							@if ($product->is_featured)
								<div
									class="absolute top-3 left-3 bg-[var(--color-accent)] text-white text-xs px-3 py-1 rounded-full shadow font-bold tracking-wide">
									★ Featured</div>
							@endif
						</div>
					</a>
					<div class="flex flex-col gap-1 text-sm p-4">
						<div
							class="inline-block text-xs font-semibold uppercase tracking-wide px-2 py-1 rounded-full bg-[var(--color-light)] text-[var(--color-primary)] mb-1">
							{{ $product->category }}</div>
						<a href="{{ route('products.show', $product->slug) }}"
							class="font-semibold text-[var(--color-primary-dark)] hover:underline">{{ $product->name }}</a>
						<div class="flex items-center justify-between mt-2">
							<div class="font-bold text-[var(--color-primary-dark)]">${{ number_format($product->price / 100, 2) }} <span
									class="text-xs font-normal text-gray-400">USD</span></div>
							<form action="{{ route('cart.add', $product->slug) }}" method="post">
								@csrf
								<button type="submit"
									class="w-8 h-8 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-lg hover:bg-[var(--color-primary-dark)] transition cursor-pointer"
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
				<p class="text-gray-400">No stuffed animals yet. Add some products to get started.</p>
			@endforelse
		</div>
	</section>

	<section class="mt-10" id="wooden">
		<div class="flex items-end justify-between mb-6">
			<div>
				<h2 class="text-2xl font-bold text-[color:var(--color-primary-dark)]">Wooden Toys</h2>
				<div class="text-gray-500 text-sm">Timeless toys made to last.</div>
			</div>
			<a href="{{ route('catalog', ['category' => 'Wooden Toys']) }}"
				class="text-[color:var(--color-primary)] hover:underline font-medium">See all toys</a>
		</div>
		<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
			@forelse ($featuredWooden as $product)
				<article class="bg-white rounded-2xl shadow flex flex-col gap-2 h-full p-0">
					<a href="{{ route('products.show', $product->slug) }}">
						<div
							class="relative w-full aspect-[4/3] bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-primary)] flex items-center justify-center rounded-t-2xl overflow-hidden">
							@php
								$img = $product->primary_image ?? ($product->images[0] ?? null);
							@endphp
							@if ($img)
								<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" loading="lazy"
									class="w-full h-full object-cover" />
							@endif
							@if ($product->is_featured)
								<div
									class="absolute top-3 left-3 bg-[var(--color-accent)] text-white text-xs px-3 py-1 rounded-full shadow font-bold tracking-wide">
									★ Featured</div>
							@endif
						</div>
					</a>
					<div class="flex flex-col gap-1 text-sm p-4">
						<div
							class="inline-block text-xs font-semibold uppercase tracking-wide px-2 py-1 rounded-full bg-[var(--color-light)] text-[var(--color-primary)] mb-1">
							{{ $product->category }}</div>
						<a href="{{ route('products.show', $product->slug) }}"
							class="font-semibold text-[var(--color-primary-dark)] hover:underline">{{ $product->name }}</a>
						<div class="flex items-center justify-between mt-2">
							<div class="font-bold text-[var(--color-primary-dark)]">${{ number_format($product->price / 100, 2) }} <span
									class="text-xs font-normal text-gray-400">USD</span></div>
							<form action="{{ route('cart.add', $product->slug) }}" method="post">
								@csrf
								<button type="submit"
									class="w-8 h-8 rounded-full bg-[var(--color-primary)] text-white flex items-center justify-center font-bold text-lg hover:bg-[var(--color-primary-dark)] transition cursor-pointer"
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
				<p class="text-gray-400">No wooden toys yet. Add some products to get started.</p>
			@endforelse
		</div>
	</section>

	<section class="mt-12" id="about">
		<div class="mb-4">
			<h2 class="text-2xl font-bold text-[var(--color-primary-dark)]">About KiddosHeaven</h2>
		</div>
		<p class="text-lg text-gray-700 mb-4 max-w-2xl">
			Welcome to KiddosHeaven! We are passionate about bringing joy, creativity, and learning to children through a curated
			selection of safe, sustainable, and delightfully designed toys. Our mission is to inspire imagination and everyday
			happiness for kids and parents alike.
		</p>
		<ul class="list-disc pl-6 text-gray-600 mb-4 max-w-2xl">
			<li>Handpicked, high-quality toys for all ages</li>
			<li>Focus on safety, sustainability, and fun</li>
			<li>Friendly customer support and fast delivery</li>
			<li>Simple, secure cash-on-delivery checkout</li>
		</ul>
		<p class="text-gray-500 max-w-2xl">
			Thank you for choosing KiddosHeaven and supporting our small business. We hope our toys bring smiles and cherished
			memories to your family!
		</p>
	</section>
@endsection
