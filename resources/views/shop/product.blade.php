@extends('layouts.app')

@section('title', $product->name . ' — KiddosHeaven')

@section('content')
	<div class="flex flex-col lg:flex-row gap-8">
		<div class="flex-1 bg-white rounded-xl shadow p-6 mb-8">
			<div class="mb-4 border-b pb-3">
				<h2 class="text-2xl font-bold text-[color:var(--color-primary-dark)]">{{ $product->name }}</h2>
				<div class="text-xs text-gray-400">{{ $product->category }}</div>
			</div>
			<div
				class="relative aspect-[4/3] bg-[color:var(--color-light)] rounded-lg overflow-hidden flex items-center justify-center mb-4">
				@php
					$mainImage = $product->primary_image ?? ($product->images[0] ?? null);
				@endphp
				<img id="mainProductImage" src="{{ $mainImage ? asset('storage/' . $mainImage) : '' }}" alt="{{ $product->name }}"
					loading="lazy" class="object-contain w-full h-full {{ $mainImage ? '' : 'hidden' }}">
				@if ($product->is_featured)
					<div class="absolute top-2 left-2 bg-[color:var(--color-accent)] text-white text-xs px-2 py-1 rounded">Favorite
					</div>
				@endif
			</div>
			@if (!empty($product->images))
				<div class="flex gap-2 mb-4">
					@foreach ($product->images as $img)
						<img src="{{ asset('storage/' . $img) }}" alt="Thumbnail"
							class="w-16 h-16 object-cover rounded cursor-pointer border border-gray-200 hover:border-[color:var(--color-primary)] preview-thumb"
							onclick="document.getElementById('mainProductImage').src=this.src">
					@endforeach
				</div>
			@endif
			@if ($product->short_description)
				<p class="text-gray-500 mb-2">
					{{ $product->short_description }}
				</p>
			@endif
			@if ($product->description)
				<p class="text-sm text-gray-700 mb-4">
					{{ $product->description }}
				</p>
			@endif
			<div class="flex items-center justify-between mt-4 p-4 rounded-lg bg-[color:var(--color-light)]">
				<div>
					<div class="text-lg font-bold text-[color:var(--color-primary)]">
						${{ number_format($product->price / 100, 2) }} <span class="text-xs font-normal">USD</span>
					</div>
					<div class="text-xs text-gray-400">Cash on Delivery only</div>
				</div>
				<form action="{{ route('cart.add', $product->slug) }}" method="post" class="flex items-center gap-2">
					@csrf
					<input type="number" name="quantity" value="1" min="1"
						class="w-14 rounded border border-gray-300 px-2 py-1 text-center">
					<button type="submit"
						class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-br from-[color:var(--color-primary)] to-[color:var(--color-accent)] text-white font-bold shadow hover:from-[color:var(--color-primary-dark)] transition">Add
						to cart</button>
				</form>
			</div>
		</div>
		@if ($related->isNotEmpty())
			<div class="w-full max-w-md bg-white rounded-xl shadow p-6 mb-8">
				<div class="mb-4 border-b pb-3">
					<h2 class="text-lg font-bold text-[color:var(--color-primary-dark)]">You may also like</h2>
				</div>
				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
					@foreach ($related as $item)
						<article class="bg-white rounded-2xl shadow flex flex-col gap-2 h-full p-0">
							<a href="{{ route('products.show', $item->slug) }}">
								<div
									class="relative w-full aspect-[4/3] bg-gradient-to-br from-[var(--color-accent)] to-[var(--color-primary)] flex items-center justify-center rounded-t-2xl overflow-hidden">
									@php
										$img = $item->primary_image ?? ($item->images[0] ?? null);
									@endphp
									@if ($img)
										<img src="{{ asset('storage/' . $img) }}" alt="{{ $item->name }}" loading="lazy"
											class="w-full h-full object-cover" />
									@endif
								</div>
							</a>
							<div class="flex flex-col gap-1 text-sm p-4">
								<div
									class="inline-block text-xs font-semibold uppercase tracking-wide px-2 py-1 rounded-full bg-[var(--color-light)] text-[var(--color-primary)] mb-1">
									{{ $item->category }}</div>
								<a href="{{ route('products.show', $item->slug) }}"
									class="font-semibold text-[var(--color-primary-dark)] hover:underline">{{ $item->name }}</a>
								<div class="font-bold text-[var(--color-primary-dark)] mt-2">${{ number_format($item->price / 100, 2) }} <span
										class="text-xs font-normal text-gray-400">USD</span></div>
							</div>
						</article>
					@endforeach
				</div>
			</div>
		@endif
	</div>
@endsection
