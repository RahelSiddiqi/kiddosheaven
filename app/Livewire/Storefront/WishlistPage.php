<?php

namespace App\Livewire\Storefront;

use App\Models\Wishlist;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Layout('layouts.livewire')]
#[Title('My Wishlist')]
class WishlistPage extends Component
{
    #[Computed]
    public function items()
    {
        return Wishlist::where('user_id', auth()->id())
            ->with('product:id,name,slug,price,images,stock_quantity,is_active')
            ->latest()
            ->get();
    }

    public function removeItem(int $wishlistId): void
    {
        Wishlist::where('id', $wishlistId)
            ->where('user_id', auth()->id())
            ->delete();

        unset($this->items);
        $this->dispatch('notify', message: 'Removed from wishlist.', type: 'success');
    }

    public function moveToCart(int $wishlistId): void
    {
        $item = Wishlist::where('id', $wishlistId)
            ->where('user_id', auth()->id())
            ->with('product:id,name,slug,price,images,stock_quantity,is_active')
            ->first();

        if (!$item || !$item->product) {
            $this->dispatch('notify', message: 'Product not found.', type: 'error');
            return;
        }

        $product = $item->product;

        if (!$product->is_active || $product->stock_quantity < 1) {
            $this->dispatch('notify', message: 'This product is out of stock.', type: 'error');
            return;
        }

        // Add to session cart using the same format as CartController
        $cart = session()->get('cart', ['items' => [], 'subtotal' => 0, 'total' => 0]);
        $key  = (string) $product->id;

        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity'] += 1;
        } else {
            $primaryImage = is_array($product->images) && count($product->images) > 0
                ? $product->images[0]
                : null;

            $cart['items'][$key] = [
                'product_id'        => $product->id,
                'variant_id'        => null,
                'name'              => $product->name,
                'slug'              => $product->slug,
                'price'             => (float) $product->price,
                'image'             => $primaryImage,
                'quantity'          => 1,
                'variant_attributes' => [],
            ];
        }

        // Recalculate totals
        $subtotal = 0;
        foreach ($cart['items'] as &$cartItem) {
            $cartItem['line_total'] = $cartItem['price'] * $cartItem['quantity'];
            $subtotal += $cartItem['line_total'];
        }
        $cart['subtotal'] = $subtotal;
        $cart['total']    = $subtotal;

        session()->put('cart', $cart);

        // Remove from wishlist
        $item->delete();
        unset($this->items);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Moved to cart!', type: 'success');
    }

    public function clearAll(): void
    {
        Wishlist::where('user_id', auth()->id())->delete();
        unset($this->items);
        $this->dispatch('notify', message: 'Wishlist cleared.', type: 'success');
    }

    public function render()
    {
        return view('livewire.storefront.wishlist-page');
    }
}
