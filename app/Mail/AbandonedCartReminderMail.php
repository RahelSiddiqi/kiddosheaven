<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbandonedCartReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly array $cartItems,
        public readonly float $cartTotal,
        public readonly ?string $couponCode = null
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You left something in your cart!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cart.abandoned',
        );
    }
}
