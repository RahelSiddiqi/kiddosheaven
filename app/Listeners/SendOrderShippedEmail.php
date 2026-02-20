<?php

namespace App\Listeners;

use App\Events\ShipmentShipped;
use App\Mail\OrderShippedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderShippedEmail implements ShouldQueue
{
    public function handle(ShipmentShipped $event): void
    {
        $order = $event->shipment->order;
        $email = $order->customer_email ?? null;

        if (!$email) {
            return;
        }

        try {
            Mail::to($email)->send(new OrderShippedMail($event->shipment));
        } catch (\Throwable $e) {
            logger()->error('Order shipped email failed', [
                'shipment_id' => $event->shipment->id,
                'order_id'    => $order->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }
}
