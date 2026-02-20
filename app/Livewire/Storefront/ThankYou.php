<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domains\Order\Models\Order;

class ThankYou extends Component
{
    public $order;

    public function mount(Order $order)
    {
        // Ensure the order belongs to the current user (if logged in)
        if (auth()->check() && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $this->order = $order->load('items.product');
    }

    public function render()
    {
        return view('livewire.storefront.thank-you');
    }
}
