<?php

namespace App\Mail;

use App\Domains\Shipping\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Shipment $shipment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Order Has Shipped! #' . $this->shipment->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.shipped',
        );
    }
}
