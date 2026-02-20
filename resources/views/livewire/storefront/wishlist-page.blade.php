<div class="space-y-8">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">My Wishlist</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                {{ $this->items->count() }} {{ Str::plural('item', $this->items->count()) }} saved
            </p>
        </div>
        <a href="{{ route('catalog') }}" wire:navigate
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            Continue Shopping
        </a>
    </div>

    @if ($this->items->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
            <div class="w-20 h-20 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-red-300 dark:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Your wishlist is empty</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Save items you love by clicking the heart icon on product pages.</p>
            <a href="{{ route('catalog') }}" wire:navigate
                class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Start Shopping
            </a>
        </div>
    @else
        {{-- Wishlist Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($this->items as $item)
                @if ($item->product)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group">
                        {{-- Product Image --}}
                        <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate class="block relative aspect-square overflow-hidden">
                            @php
                                $imagePath = $item->product->images[0] ?? null;
                            @endphp
                            @if ($imagePath)
                                <img src="{{ Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath) }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Stock Status Badge --}}
                            @if (!$item->product->is_active || $item->product->stock_quantity <= 0)
                                <div class="absolute top-3 left-3 px-2 py-1 bg-gray-900/80 text-white text-xs font-medium rounded">
                                    Out of Stock
                                </div>
                            @endif
                        </a>

                        {{-- Product Info --}}
                        <div class="p-4">
                            <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate
                               class="block font-semibold text-gray-900 dark:text-white hover:text-primary dark:hover:text-primary transition line-clamp-2 mb-2">
                                {{ $item->product->name }}
                            </a>

                            <div class="text-lg font-bold text-primary mb-4">
                                @price($item->product->price)
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate
                                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition text-sm {{ (!$item->product->is_active || $item->product->stock_quantity <= 0) ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    View
                                </a>
                                <button
                                    wire:click="removeItem({{ $item->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="removeItem({{ $item->id }})"
                                    class="p-2.5 rounded-lg border border-red-200 dark:border-red-800 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition"
                                    title="Remove from wishlist">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
