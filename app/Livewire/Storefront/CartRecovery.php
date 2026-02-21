<?php

namespace App\Livewire\Storefront;

use App\Domains\Marketing\Models\AbandonedCart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Recover Your Cart')]
class CartRecovery extends Component
{
    public ?AbandonedCart $cart = null;
    public bool $recovered = false;
    public bool $invalid   = false;

    public function mount(string $token): void
    {
        $this->cart = AbandonedCart::where('token', $token)
            ->where('status', 'active')
            ->first();

        if (! $this->cart) {
            $this->invalid = true;
            return;
        }

        // Mark as recovered
        $this->cart->update(['status' => 'recovered', 'recovered_at' => now()]);

        // Restore cart session
        $items = json_decode($this->cart->cart_data, true) ?? [];
        Session::put('cart', $items);

        $this->recovered = true;
    }

    public function render()
    {
        return view('livewire.storefront.cart-recovery');
    }
}
