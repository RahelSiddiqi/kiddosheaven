@extends('layouts.app')

@section('title', 'My Wishlist — Kiddo\'s Heaven')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">My Wishlist</h1>
            <p class="text-gray-500 mt-1 text-sm sm:text-base">{{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }} saved</p>
        </div>
        @if($wishlistItems->isNotEmpty())
            <button type="button" onclick="clearWishlist()"
                class="inline-flex items-center gap-2 px-4 py-2.5 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition text-sm font-medium w-full sm:w-auto justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Clear All
            </button>
        @endif
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Wishlist Items --}}
    @if($wishlistItems->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 rounded-full bg-pink-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Your wishlist is empty</h3>
            <p class="text-gray-500 mb-6">Save your favorite products here for later</p>
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Browse Products
            </a>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($wishlistItems as $item)
                @php $product = $item->product; @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group hover:shadow-md transition" data-wishlist-item="{{ $product->id }}">
                    {{-- Product Image --}}
                    <div class="relative aspect-square overflow-hidden bg-gray-100">
                        <a href="{{ route('products.show', $product->slug) }}">
                            @if($product->primary_image)
                                <img src="{{ asset('storage/' . $product->primary_image) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <span class="text-gray-400 text-4xl">📦</span>
                                </div>
                            @endif
                        </a>

                        {{-- Remove Button --}}
                        <button type="button" onclick="removeFromWishlist({{ $product->id }})"
                            class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 hover:bg-white flex items-center justify-center shadow-sm hover:shadow-md transition">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        {{-- Stock Badge --}}
                        @if($product->stock_quantity <= 0)
                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">
                                Out of Stock
                            </span>
                        @elseif($product->stock_quantity <= 5)
                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-orange-500 text-white text-xs font-semibold rounded-full">
                                Only {{ $product->stock_quantity }} left
                            </span>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="p-4 space-y-3">
                        <a href="{{ route('products.show', $product->slug) }}" class="block">
                            <h3 class="font-semibold text-gray-900 line-clamp-2 hover:text-primary transition">{{ $product->name }}</h3>
                        </a>

                        {{-- Price --}}
                        <div class="flex items-baseline gap-2">
                            <span class="text-lg font-bold text-primary">৳{{ number_format($product->price, 0) }}</span>
                            @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                <span class="text-sm text-gray-400 line-through">৳{{ number_format($product->compare_at_price, 0) }}</span>
                                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                    {{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}% OFF
                                </span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            @if($product->stock_quantity > 0)
                                <button type="button" onclick="moveToCart({{ $product->id }})"
                                    class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition text-sm font-medium">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Add to Cart
                                </button>
                            @else
                                <button disabled
                                    class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-500 rounded-lg cursor-not-allowed text-sm font-medium">
                                    Out of Stock
                                </button>
                            @endif
                            <a href="{{ route('products.show', $product->slug) }}"
                                class="px-4 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                View
                            </a>
                        </div>

                        {{-- Added Date --}}
                        <p class="text-xs text-gray-400">
                            Added {{ $item->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Back to Account --}}
    <div class="mt-8">
        <a href="{{ route('account') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Account
        </a>
    </div>
@endsection

@push('scripts')
<script>
function removeFromWishlist(productId) {
    if (!confirm('Remove this item from your wishlist?')) return;

    fetch(`/wishlist/remove/${productId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Remove item from DOM
            const item = document.querySelector(`[data-wishlist-item="${productId}"]`);
            if (item) {
                item.remove();
            }

            // Update count or reload if empty
            const remaining = document.querySelectorAll('[data-wishlist-item]').length;
            if (remaining === 0) {
                location.reload();
            }

            // Show success message
            showMessage(data.message, 'success');
        }
    })
    .catch(err => {
        showMessage('Error removing item from wishlist', 'error');
    });
}

function moveToCart(productId) {
    fetch(`/wishlist/move-to-cart/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showMessage('Product moved to cart!', 'success');
            // Remove from wishlist display
            const item = document.querySelector(`[data-wishlist-item="${productId}"]`);
            if (item) {
                item.remove();
            }

            // Reload if empty
            const remaining = document.querySelectorAll('[data-wishlist-item]').length;
            if (remaining === 0) {
                setTimeout(() => location.reload(), 1000);
            }
        }
    })
    .catch(err => {
        showMessage('Error moving item to cart', 'error');
    });
}

function clearWishlist() {
    if (!confirm('Are you sure you want to clear your entire wishlist?')) return;

    fetch('/wishlist/clear', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => {
        showMessage('Error clearing wishlist', 'error');
    });
}

function showMessage(message, type = 'success') {
    const div = document.createElement('div');
    div.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white font-medium animate-fade-in`;
    div.textContent = message;
    document.body.appendChild(div);

    setTimeout(() => {
        div.remove();
    }, 3000);
}
</script>
@endpush
