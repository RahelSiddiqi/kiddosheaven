<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Domains\Order\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderHistory extends Component
{
    use WithPagination;

    #[Url]
    public $status = '';

    public function setStatus($status)
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::where('user_id', Auth::id())
            ->with('items')
            ->latest();

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $orders = $query->paginate(10);

        return view('livewire.storefront.order-history', compact('orders'));
    }
}
