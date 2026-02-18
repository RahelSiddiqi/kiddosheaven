@props(['product', 'showQuickAdd' => true, 'badge' => null])

@php
	$img          = $product->primary_image ?? ($product->images[0] ?? null);
	$isPercentage = ($product->discount_type ?? 'percentage') === 'percentage';
	$hasDiscount  = $product->discount_price && $product->discount_price > 0
	                && ($isPercentage ? $product->discount_price < 100 : $product->discount_price < $product->price);

	if ($hasDiscount) {
	    if ($isPercentage) {
	        $discountPct   = round($product->discount_price);
	        $originalPrice = round($product->price / (1 - $product->discount_price / 100));
	    } else {
	        $originalPrice = $product->price + $product->discount_price;
	        $discountPct   = round(($product->discount_price / $originalPrice) * 100);
	    }
	} else {
	    $discountPct   = 0;
	    $originalPrice = null;
	}

	$isNew       = $product->created_at && $product->created_at->diffInDays() < 7;
	$avgRating   = $product->average_rating ?? 0;
	$reviewCount = $product->approved_reviews_count ?? ($product->review_count ?? 0);
@endphp

<article
	class="group bg-white rounded-xl shadow-sm border border-[#E8E6E6] hover:shadow-lg hover:border-[#018790]/30 transition-all duration-300 relative">
	{{-- Image --}}
	<a href="{{ route('products.show', $product->slug) }}" class="block cursor-pointer">
		<div class="relative w-full aspect-square bg-[#F5F5F5] overflow-hidden rounded-t-xl">
			@if ($img)
				<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}"
					class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
			@else
				<div class="w-full h-full flex items-center justify-center">
					<svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
							d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
					</svg>
				</div>
			@endif

			{{-- Badges --}}
			<div class="absolute top-2 left-2 flex flex-col gap-1">
				@if ($badge)
					<x-shop.ui.badge :type="$badge" />
				@endif
				@if ($isNew && $badge !== 'new')
					<x-shop.ui.badge type="new" />
				@endif
				@if ($hasDiscount)
					<x-shop.ui.badge type="sale" :value="$discountPct" />
				@endif
				@if ($product->stock_quantity <= 0)
					<x-shop.ui.badge type="soldout" />
				@endif
			</div>
		</div>
	</a>

	{{-- Info --}}
	<div class="p-3 md:p-4 pb-5">

		{{-- Brand --}}
		@if ($product->relationLoaded('brand') && $product->brand)
			<p class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">{{ $product->brand->name }}</p>
		@endif

		{{-- Name --}}
		<a href="{{ route('products.show', $product->slug) }}"
			class="block font-semibold text-sm md:text-base text-[#1E1414] hover:text-[#018790] line-clamp-2 transition cursor-pointer">
			{{ $product->name }}
		</a>

		{{-- Price --}}
		<div class="flex items-center justify-between mt-2 md:mt-3">
			<div class="flex flex-col">
				<span class="text-base md:text-lg font-bold text-[#018790]">৳ {{ number_format($product->price, 0) }}</span>
				@if ($hasDiscount)
					<span class="text-xs text-[#888] line-through">৳ {{ number_format($originalPrice, 0) }}</span>
				@endif
			</div>
			@if ($reviewCount > 0)
				<div class="flex items-center gap-1 text-yellow-500 text-xs md:text-sm">
					<svg class="w-3.5 h-3.5 md:w-4 md:h-4 fill-current" viewBox="0 0 20 20">
						<path
							d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
					</svg>
					<span class="text-[#666] ml-0.5">{{ number_format($avgRating, 1) }}</span>
				</div>
			@endif
		</div>
	</div>

	{{-- Cart Button (card-level, adds to cart and redirects to cart) --}}
	@if ($showQuickAdd && $product->is_in_stock)
		<form action="{{ route('cart.add', $product->slug) }}" method="post" class="absolute bottom-3 right-2 z-10">
			@csrf
			@if ($product->product_type === 'variable')
				@php
					$variantsRelation = $product->getRelation('variants');
					$defaultVariant = $variantsRelation
					    ? $variantsRelation->firstWhere('is_default', true) ?? $variantsRelation->first()
					    : null;
				@endphp
				@if ($defaultVariant)
					<input type="hidden" name="variant_id" value="{{ $defaultVariant->id }}">
				@endif
			@endif
			<button type="submit"
				class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-[#018790] shadow-lg flex items-center justify-center hover:bg-[#017079] transition-all duration-300 cursor-pointer">
				<svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
				</svg>
			</button>
		</form>
	@endif
</article>
