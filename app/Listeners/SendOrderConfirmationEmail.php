<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderPlacedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $email = $order->customer_email ?? null;

        if (!$email) return;

        try {
            Mail::to($email)->send(new OrderPlacedMail($order));
        } catch (\Throwable $e) {
            // Log but don't fail the order
            logger()->error('Order confirmation email failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
