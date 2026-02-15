<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domains\Order\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderDetail extends Component
{
    public Order $order;

    public function mount($id)
    {
        $this->order = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->findOrFail($id);
    }

    public function cancelOrder()
    {
        if (!in_array($this->order->status, ['pending', 'processing'])) {
            session()->flash('error', 'Order cannot be cancelled at this stage.');
            return;
        }

        $this->order->update(['status' => 'cancelled']);
        session()->flash('success', 'Order cancelled successfully.');
    }

    public function render()
    {
        return view('livewire.storefront.order-detail');
    }
}
