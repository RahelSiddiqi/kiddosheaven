<?php

namespace App\Mail;

use App\Domains\Marketing\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly AbandonedCart $cart,
        public readonly int $reminderNumber,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            1 => 'You left something behind…',
            2 => 'Your cart is waiting for you',
            3 => 'Last chance — your cart expires soon',
        ];

        return new Envelope(
            subject: $subjects[$this->reminderNumber] ?? 'Your cart',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.abandoned-cart',
            with: [
                'cart'           => $this->cart,
                'recoveryUrl'    => url('/cart/recover/' . $this->cart->token),
                'reminderNumber' => $this->reminderNumber,
                'items'          => $this->cart->items ?? [],
            ]
        );
    }
}
