<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domains\Product\Models\Product;

class CartPage extends Component
{
    public $cart = [];
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session('cart', ['items' => []]);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;

        foreach ($this->cart['items'] ?? [] as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        $this->tax = $this->subtotal * 0.15; // 15% tax
        $this->total = $this->subtotal + $this->tax;
    }

    public function updateQuantity($key, $quantity)
    {
        $quantity = max(1, intval($quantity));

        if (isset($this->cart['items'][$key])) {
            $this->cart['items'][$key]['quantity'] = $quantity;
            session(['cart' => $this->cart]);
            $this->loadCart();
            $this->dispatch('cart-updated');
        }
    }

    public function removeItem($key)
    {
        if (isset($this->cart['items'][$key])) {
            unset($this->cart['items'][$key]);
            session(['cart' => $this->cart]);
            $this->loadCart();
            $this->dispatch('cart-updated');
            session()->flash('cart-success', 'Item removed from cart');
        }
    }

    public function clearCart()
    {
        session(['cart' => ['items' => []]]);
        $this->loadCart();
        $this->dispatch('cart-updated');
        session()->flash('cart-success', 'Cart cleared');
    }

    public function render()
    {
        return view('livewire.storefront.cart-page');
    }
}
