<?php

namespace App\Livewire\Admin\Order;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Order Detail - Admin')]
class OrderShow extends Component
{
    #[Locked]
    public int $orderId;

    public ?object $order = null;

    // Status update
    public string $newStatus    = '';
    public string $statusNote   = '';
    public string $trackingNumber = '';

    protected const VALID_STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;
        $this->loadOrder();
    }

    private function loadOrder(): void
    {
        // Try domain model first
        $modelClass = class_exists(\App\Domains\Order\Models\Order::class)
            ? \App\Domains\Order\Models\Order::class
            : \App\Models\Order::class;

        $this->order = $modelClass::with([
            'items.product',
            'items.variant',
            'statusHistory',
            'user',
        ])->withoutGlobalScope('site')->findOrFail($this->orderId);

        $this->newStatus = $this->order->status;
    }

    public function updateStatus(): void
    {
        if (!in_array($this->newStatus, self::VALID_STATUSES)) {
            $this->dispatch('notify', message: 'Invalid status.', type: 'error');
            return;
        }

        if ($this->newStatus === $this->order->status) {
            return;
        }

        try {
            if ($this->newStatus === 'cancelled') {
                DB::transaction(fn() => app(OrderService::class)->cancel($this->orderId));
            } else {
                $this->order->update(['status' => $this->newStatus]);

                // Record in history table if it exists
                if ($this->order->statusHistory !== null) {
                    try {
                        $this->order->statusHistory()->create([
                            'status' => $this->newStatus,
                            'note'   => $this->statusNote ?: null,
                            'user_id' => auth()->id(),
                        ]);
                    } catch (\Exception) {
                        // status_history table may not have all columns yet
                    }
                }
            }

            $this->loadOrder();
            $this->statusNote = '';
            $this->dispatch('notify', message: 'Order status updated.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Failed to update status: ' . $e->getMessage(), type: 'error');
        }
    }

    public function ship(): void
    {
        if (!$this->trackingNumber) {
            $this->addError('trackingNumber', 'Tracking number is required.');
            return;
        }
        $this->order->update([
            'status' => 'shipped',
            'tracking_number' => $this->trackingNumber,
        ]);
        try {
            $this->order->statusHistory()->create([
                'status' => 'shipped',
                'note' => 'Shipped with tracking: ' . $this->trackingNumber,
            ]);
        } catch (\Exception $e) {}
        $this->trackingNumber = '';
        $this->loadOrder();
        $this->dispatch('notify', message: 'Order marked as shipped.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.order.order-show');
    }
}
