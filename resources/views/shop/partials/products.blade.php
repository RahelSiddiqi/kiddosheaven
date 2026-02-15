{{-- shop/partials/products.blade.php --}}
{{-- This partial contains the product grid and empty state for both mobile and desktop --}}
@if ($products->count() > 0)
	<div class="grid grid-cols-2 gap-3 lg:grid-cols-3 lg:gap-6">
		@foreach ($products as $product)
			<article
				class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all duration-300">
				<a href="{{ route('products.show', $product->slug) }}">
					<div class="relative w-full aspect-4/3 md:aspect-4/3 bg-gray-100 overflow-hidden">
						@php
							$img = $product->primary_image ?? ($product->images[0] ?? null);
						@endphp
						@if ($img)
							<img src="{{ asset($img) }}" alt="{{ $product->name }}" loading="lazy"
								class="w-full h-full object-cover group-hover:scale-110 transition duration-500 z-0">
						@else
							<div class="w-full h-full flex items-center justify-center">
								<span class="text-3xl md:text-4xl">🧸</span>
							</div>
						@endif
						{{-- Badges --}}
						<div class="absolute top-2 left-2 flex flex-col gap-1.5">
							@if ($product->is_featured)
								<span class="px-2 py-0.5 rounded-full bg-yellow-400 text-white text-[10px] md:text-xs font-bold shadow">★</span>
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
						class="font-semibold text-sm md:text-base text-gray-800 hover:text-(--color-primary) line-clamp-2 group-hover:text-(--color-primary) transition">{{ $product->name }}</a>
					<div class="flex items-center justify-between mt-2 md:mt-3">
						<span
							class="text-base md:text-lg font-bold text-(--color-primary-dark)">৳{{ number_format($product->price, 2) }}</span>
						@if ($product->review_count > 0)
							<div class="flex items-center gap-1 text-yellow-400 text-xs md:text-sm">
								<svg class="w-3 h-3 md:w-4 md:h-4 fill-current" viewBox="0 0 20 20">
									<path
										d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.544z" />
								</svg>
								<span class="text-gray-600 text-xs">{{ number_format($product->average_rating, 1) }}</span>
							</div>
						@endif
					</div>
					{{-- Mobile Quick Add Button --}}
					<form action="{{ route('cart.add', $product->slug) }}" method="post" class="mt-3 md:hidden">
						@csrf
						<button type="submit"
							class="w-full py-2.5 rounded-lg bg-(--color-primary) text-white text-sm font-medium flex items-center justify-center gap-2 active:scale-95 transition">
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
					d="M4 4 v5h .582 m15 .356 2 A8 .001 8.001 0 004.582 9 m0 0 H9m11 11 v - 5 h - .581 m0 0 a8 .003 8.003 0 01 -15.357 - 2 m15 .357 2 H15" />
			</svg>
			Clear Filters
		</a>
	</div>
@endif
