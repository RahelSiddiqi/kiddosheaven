<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmation
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        Log::info("Order confirmation sent for order #{$order->order_number}", [
            'order_id' => $order->id,
            'customer_email' => $order->customer_email,
            'total' => $order->total_amount,
        ]);
    }
}
