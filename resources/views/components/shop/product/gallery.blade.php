@props(['product'])

@php
    $mainImage = $product->primary_image ?? ($product->images[0] ?? null);
    $allImages = $product->images ?? [];
@endphp

<div x-data="{ activeImage: '{{ $mainImage ? asset('storage/' . $mainImage) : '' }}' }"
    @variant-image-change.window="activeImage = $event.detail.src"
    class="space-y-3">
    {{-- Main Image --}}
    <div class="relative bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="aspect-square bg-gray-50">
            <template x-if="activeImage">
                <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-contain">
            </template>
            @if (!$mainImage)
                <div x-show="!activeImage" class="w-full h-full flex items-center justify-center">
                    <span class="text-6xl">🧸</span>
                </div>
            @endif
        </div>

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-2">
            @if ($product->is_featured)
                <x-shop.ui.badge type="featured" value="Featured" />
            @endif
            @if ($product->created_at && $product->created_at->diffInDays() < 14)
                <x-shop.ui.badge type="new" value="New" />
            @endif
            @if ($product->discount_price && $product->discount_price > 0)
                @php
                    $isPercentage = ($product->discount_type ?? 'percentage') === 'percentage';
                    $hasDiscount  = $isPercentage ? $product->discount_price < 100 : $product->discount_price < $product->price;
                    $discountPct  = $hasDiscount
                        ? ($isPercentage
                            ? round($product->discount_price)
                            : round(($product->discount_price / ($product->price + $product->discount_price)) * 100))
                        : 0;
                @endphp
                @if ($hasDiscount)
                    <x-shop.ui.badge type="sale" :value="$discountPct" />
                @endif
            @endif
        </div>

        {{-- Wishlist & Share --}}
        <div class="absolute top-3 right-3 flex flex-col gap-2">
            @auth
                <button class="w-10 h-10 rounded-full bg-white/90 backdrop-blur shadow-sm flex items-center justify-center hover:bg-white transition"
                    title="Add to Wishlist"
                    x-data="{
                        inWishlist: false,
                        loading: false,
                        async toggle() {
                            if (this.loading) return;
                            this.loading = true;
                            try {
                                const res = await fetch('{{ route('wishlist.toggle', $product->id) }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                });
                                const data = await res.json();
                                if (data.success) {
                                    this.inWishlist = data.in_wishlist;
                                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: data.message } }));
                                }
                            } catch(e) {
                                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Something went wrong' } }));
                            } finally { this.loading = false; }
                        }
                    }"
                    @click="toggle()">
                    <svg class="w-5 h-5 transition" :class="inWishlist ? 'text-red-500 fill-current' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            @else
                <a href="{{ route('login') }}" class="w-10 h-10 rounded-full bg-white/90 backdrop-blur shadow-sm flex items-center justify-center hover:bg-white transition" title="Sign in to add to Wishlist">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </a>
            @endauth
            <button class="w-10 h-10 rounded-full bg-white/90 backdrop-blur shadow-sm flex items-center justify-center hover:bg-white transition" title="Share"
                onclick="navigator.share ? navigator.share({ title: '{{ addslashes($product->name) }}', url: window.location.href }) : (navigator.clipboard.writeText(window.location.href) && alert('Link copied!'))">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
            </button>
        </div>
    </div>

    {{-- Thumbnails --}}
    @if (count($allImages) > 1)
        <div class="flex gap-2.5 overflow-x-auto pl-2 pt-1 pb-1 scrollbar-hide">
            @foreach ($allImages as $img)
                <button type="button"
                    @click="activeImage = '{{ asset('storage/' . $img) }}'"
                    :class="activeImage === '{{ asset('storage/' . $img) }}' ? 'ring-2 ring-primary border-transparent' : 'border-gray-200 hover:border-primary'"
                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden border-2 transition flex-shrink-0">
                    <img src="{{ asset('storage/' . $img) }}" alt="Thumbnail" class="w-full h-full object-cover">
                </button>
            @endforeach
        </div>
    @endif
</div>
