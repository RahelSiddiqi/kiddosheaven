@extends('layouts.app')

@section('title', 'Catalog — KiddosHeaven')

@section('content')
	<div class="flex justify-between items-baseline mb-5 gap-4 flex-wrap">
		<div>
			<h2 class="text-2xl font-bold text-[--color-primary-dark] mb-1">Catalog</h2>
			<div class="text-gray-500 text-sm">Browse all of our toys and pick your child&apos;s next favorite.</div>
		</div>
		@if ($categories->isNotEmpty())
			<div class="inline-flex p-1 bg-white rounded-full shadow gap-1">
				<a href="{{ route('catalog') }}">
					<button
						class="px-3 py-1 text-xs rounded-full font-medium transition {{ !$activeCategory ? 'bg-gradient-to-br from-[--color-primary] to-[--color-accent] text-white' : 'hover:bg-gray-100' }}">All
						toys</button>
				</a>
				@foreach ($categories as $category)
					<a href="{{ route('catalog', ['category' => $category]) }}">
						<button
							class="px-3 py-1 text-xs rounded-full font-medium transition {{ $activeCategory === $category ? 'bg-gradient-to-br from-[--color-primary] to-[--color-accent] text-white' : 'hover:bg-gray-100' }}">
							{{ $category }}
						</button>
					</a>
				@endforeach
			</div>
		@endif
	</div>

	<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
		@forelse ($products as $product)
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
			<p class="text-gray-400">No toys found in this category yet.</p>
		@endforelse
	</div>

	<div class="mt-8">
		{{ $products->links('vendor.pagination.default') }}
	</div>
@endsection
