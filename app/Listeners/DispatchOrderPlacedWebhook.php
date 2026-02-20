<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\DispatchWebhook;

class DispatchOrderPlacedWebhook
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        if (!$order->site_id) return;

        DispatchWebhook::dispatch($order->site_id, 'order.placed', [
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'status' => $order->status,
            'customer_name' => $order->customer_name ?? null,
            'created_at' => $order->created_at->toIso8601String(),
        ]);
    }
}
