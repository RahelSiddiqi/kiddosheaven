<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Models\OrderStatusHistory;

class UpdateOrderStatusHistory
{
    public function handle(OrderStatusChanged $event): void
    {
        OrderStatusHistory::create([
            'order_id' => $event->order->id,
            'status' => $event->newStatus,
            'note' => "Status changed from {$event->oldStatus} to {$event->newStatus}",
            'created_by' => auth()->id(),
        ]);
    }
}
