<?php

namespace App\Listeners;

use App\Events\ShipmentDelivered;
use App\Mail\OrderDeliveredMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderDeliveredEmail implements ShouldQueue
{
    public function handle(ShipmentDelivered $event): void
    {
        $order = $event->shipment->order;
        $email = $order->customer_email ?? null;

        if (!$email) {
            return;
        }

        try {
            Mail::to($email)->send(new OrderDeliveredMail($order));
        } catch (\Throwable $e) {
            logger()->error('Order delivered email failed', [
                'shipment_id' => $event->shipment->id,
                'order_id'    => $order->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }
}
