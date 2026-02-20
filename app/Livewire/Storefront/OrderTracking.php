<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use App\Domains\Order\Models\Order;

#[Layout('layouts.livewire')]
#[Title('Track Your Order')]
class OrderTracking extends Component
{
    #[Url(as: 'order_id')]
    public string $orderNumber = '';

    public ?Order $order = null;
    public bool $searched = false;

    public function track(): void
    {
        $this->searched = true;
        $this->order = null;

        $q = trim($this->orderNumber);
        if (empty($q)) {
            return;
        }

        $this->order = Order::where('order_number', $q)
            ->orWhere('id', is_numeric($q) ? (int) $q : 0)
            ->with(['items.product', 'statusHistory'])
            ->first();
    }

    public function mount(): void
    {
        if ($this->orderNumber !== '') {
            $this->track();
        }
    }

    public function render()
    {
        return view('livewire.storefront.order-tracking');
    }
}
