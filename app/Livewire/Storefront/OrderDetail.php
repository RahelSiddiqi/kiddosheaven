<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domains\Order\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderDetail extends Component
{
    public Order $order;

    public function mount($id): void
    {
        $this->order = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->findOrFail($id);
    }

    public function cancelOrder(): void
    {
        if (!in_array($this->order->status, ['pending', 'processing'])) {
            $this->dispatch('notify', message: 'Order cannot be cancelled at this stage.', type: 'error');
            return;
        }

        try {
            DB::transaction(fn() => app(OrderService::class)->cancel($this->order->id));
            $this->order->refresh();
            $this->dispatch('notify', message: 'Order cancelled successfully.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Unable to cancel order. Please contact support.', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.storefront.order-detail');
    }
}
