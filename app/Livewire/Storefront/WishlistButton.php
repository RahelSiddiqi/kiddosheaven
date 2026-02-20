<?php

namespace App\Livewire\Storefront;

use App\Models\Wishlist;
use Livewire\Component;
use Livewire\Attributes\Computed;

class WishlistButton extends Component
{
    public int $productId;

    #[Computed]
    public function isInWishlist(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return Wishlist::where('user_id', auth()->id())
            ->where('product_id', $this->productId)
            ->exists();
    }

    public function toggle(): void
    {
        if (!auth()->check()) {
            $this->dispatch('notify', message: 'Please log in to save items.', type: 'info');
            return;
        }

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $this->productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->dispatch('notify', message: 'Removed from wishlist.', type: 'success');
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $this->productId,
            ]);
            $this->dispatch('notify', message: 'Added to wishlist!', type: 'success');
        }

        unset($this->isInWishlist);
    }

    public function render()
    {
        return view('livewire.storefront.wishlist-button');
    }
}
