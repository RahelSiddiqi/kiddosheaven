<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\DispatchWebhook;

class DispatchOrderStatusChangedWebhook
{
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        if (!$order->site_id) return;

        DispatchWebhook::dispatch($order->site_id, 'order.status_changed', [
            'order_number'  => $order->order_number,
            'old_status'    => $event->oldStatus,
            'new_status'    => $event->newStatus,
            'total_amount'  => $order->total_amount,
            'customer_name' => $order->customer_name ?? null,
            'updated_at'    => $order->updated_at->toIso8601String(),
        ]);
    }
}
