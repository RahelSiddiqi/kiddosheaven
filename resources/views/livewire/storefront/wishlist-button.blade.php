<button
    wire:click="toggle"
    wire:loading.attr="disabled"
    title="{{ $this->isInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
    class="p-2 rounded-full border transition-colors {{ $this->isInWishlist ? 'bg-red-50 border-red-300 text-red-500 hover:bg-red-100' : 'bg-white border-gray-200 text-gray-400 hover:text-red-400 hover:border-red-200' }} dark:bg-gray-800 dark:border-gray-700"
>
    <svg class="w-5 h-5" fill="{{ $this->isInWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
    <span wire:loading wire:target="toggle" class="sr-only">Loading...</span>
</button>
