<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\On;

class CartDrawer extends Component
{
    public $isOpen = false;
    public $items = [];
    public $subtotal = 0;
    public $itemCount = 0;

    public function mount()
    {
        $this->loadCart();
    }

    #[On('open-cart-drawer')]
    public function open()
    {
        $this->isOpen = true;
        $this->loadCart();
    }

    public function close()
    {
        $this->isOpen = false;
    }

    #[On('cart-updated')]
    public function loadCart()
    {
        $cart = session('cart', []);
        $this->items = $cart['items'] ?? [];
        $this->subtotal = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $this->items));
        $this->itemCount = array_sum(array_column($this->items, 'quantity'));
    }

    public function removeItem($key)
    {
        $cart = session('cart', []);
        unset($cart['items'][$key]);
        session(['cart' => $cart]);

        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function updateQuantity($key, $quantity)
    {
        if ($quantity < 1) {
            $this->removeItem($key);
            return;
        }

        $cart = session('cart', []);
        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity'] = $quantity;
            session(['cart' => $cart]);
        }

        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.storefront.cart-drawer');
    }
}
