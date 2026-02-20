<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Services\Cart\CartService;

class CartPage extends Component
{
    public $cart     = ['items' => []];
    public $subtotal = 0;
    public $tax      = 0;
    public $shipping = 0;
    public $total    = 0;

    public function mount(): void
    {
        $this->loadCart();
    }

    public function loadCart(): void
    {
        $items = app(CartService::class)->getItems()->map(fn($item) => [
            'product_id' => $item['product_id'],
            'name'       => $item['product']->name,
            'image'      => $item['product']->image_path,
            'price'      => (float) $item['price'],
            'quantity'   => (int)   $item['quantity'],
            'subtotal'   => (float) $item['subtotal'],
        ])->values()->all();

        $this->cart = ['items' => $items];
        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = array_sum(array_column($this->cart['items'], 'subtotal'));
        $this->tax      = round($this->subtotal * 0.15, 2);
        $this->shipping = $this->subtotal >= 1000 ? 0 : 100;
        $this->total    = $this->subtotal + $this->tax + $this->shipping;
    }

    public function updateQuantity($productId, $quantity): void
    {
        $quantity = max(1, intval($quantity));
        app(CartService::class)->updateItem((int) $productId, $quantity);
        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function removeItem($productId): void
    {
        app(CartService::class)->removeItem((int) $productId);
        $this->loadCart();
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Item removed from cart', type: 'info');
    }

    public function clearCart(): void
    {
        app(CartService::class)->clear();
        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.storefront.cart-page');
    }
}
