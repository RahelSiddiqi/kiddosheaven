<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\Cart\CartService;

class CartDrawer extends Component
{
    public $isOpen    = false;
    public $items     = [];
    public $subtotal  = 0;
    public $itemCount = 0;

    public function mount(): void
    {
        $this->loadCart();
    }

    #[On('open-cart-drawer')]
    public function open(): void
    {
        $this->isOpen = true;
        $this->loadCart();
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    #[On('cart-updated')]
    public function loadCart(): void
    {
        $cartService  = app(CartService::class);
        $this->items  = $cartService->getItems()->map(fn($item) => [
            'product_id' => $item['product_id'],
            'name'       => $item['product']->name,
            'image'      => $item['product']->image_path,
            'price'      => (float) $item['price'],
            'quantity'   => (int)   $item['quantity'],
            'subtotal'   => (float) $item['subtotal'],
        ])->values()->all();

        $this->subtotal  = array_sum(array_column($this->items, 'subtotal'));
        $this->itemCount = array_sum(array_column($this->items, 'quantity'));
    }

    public function removeItem($productId): void
    {
        app(CartService::class)->removeItem((int) $productId);
        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function updateQuantity($productId, $quantity): void
    {
        if ((int) $quantity < 1) {
            $this->removeItem($productId);
            return;
        }

        app(CartService::class)->updateItem((int) $productId, (int) $quantity);
        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.storefront.cart-drawer');
    }
}
