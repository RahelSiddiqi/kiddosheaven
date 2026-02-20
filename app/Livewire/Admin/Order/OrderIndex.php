<?php

namespace App\Livewire\Admin\Order;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Order\Models\Order;

#[Layout('admin.layouts.app')]
#[Title('Orders - Admin')]
class OrderIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sort = 'newest';

    public array $selectedIds = [];
    public array $selectedOrderIds = [];
    public string $bulkStatus = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function updateStatus(int $id, string $status): void
    {
        $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed)) return;

        $modelClass = class_exists(\App\Domains\Order\Models\Order::class)
            ? \App\Domains\Order\Models\Order::class
            : \App\Models\Order::class;

        $order = $modelClass::withoutGlobalScopes()->findOrFail($id);
        $order->update(['status' => $status]);
        unset($this->orders);
        $this->dispatch('notify', message: 'Order status updated.', type: 'success');
    }

    public function bulkUpdateStatus(): void
    {
        if (empty($this->selectedOrderIds) || !$this->bulkStatus) return;
        $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($this->bulkStatus, $allowed)) return;

        $modelClass = class_exists(\App\Domains\Order\Models\Order::class)
            ? \App\Domains\Order\Models\Order::class
            : \App\Models\Order::class;
        $modelClass::whereIn('id', $this->selectedOrderIds)->update(['status' => $this->bulkStatus]);
        $count = count($this->selectedOrderIds);
        $status = $this->bulkStatus;
        $this->selectedOrderIds = [];
        $this->bulkStatus = '';
        $this->dispatch('notify', message: "$count orders updated to {$status}.", type: 'success');
    }

    public function render()
    {
        $orders = Order::withoutGlobalScope('site')
            ->with('user')
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%")
                  ->orWhere('customer_email', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"));
            }))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->sort === 'newest', fn($q) => $q->latest())
            ->when($this->sort === 'oldest', fn($q) => $q->oldest())
            ->when($this->sort === 'total-high', fn($q) => $q->orderByDesc('total_amount'))
            ->when($this->sort === 'total-low', fn($q) => $q->orderBy('total_amount'))
            ->paginate(20);

        $stats = [
            'total'      => Order::withoutGlobalScope('site')->count(),
            'pending'    => Order::withoutGlobalScope('site')->where('status', 'pending')->count(),
            'processing' => Order::withoutGlobalScope('site')->where('status', 'processing')->count(),
            'shipped'    => Order::withoutGlobalScope('site')->where('status', 'shipped')->count(),
            'delivered'  => Order::withoutGlobalScope('site')->where('status', 'delivered')->count(),
            'cancelled'  => Order::withoutGlobalScope('site')->where('status', 'cancelled')->count(),
            'revenue'    => Order::withoutGlobalScope('site')->whereNotIn('status', ['cancelled'])->sum('total_amount'),
        ];

        return view('livewire.admin.order.order-index', compact('orders', 'stats'));
    }
}
