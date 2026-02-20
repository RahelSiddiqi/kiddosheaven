<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        if (empty($order->customer_email)) {
            Log::info("Order #{$order->order_number}: no customer email, skipping confirmation.");
            return;
        }

        try {
            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            Log::info("Order confirmation email sent for order #{$order->order_number}", [
                'order_id' => $order->id,
                'customer_email' => $order->customer_email,
            ]);
        } catch (\Throwable $e) {
            Log::error("Failed to send order confirmation for order #{$order->order_number}: " . $e->getMessage());
        }
    }
}
