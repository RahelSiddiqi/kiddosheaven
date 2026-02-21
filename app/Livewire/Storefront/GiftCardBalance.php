<?php

namespace App\Livewire\Storefront;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Domains\GiftCard\Services\GiftCardService;

#[Layout('layouts.app')]
#[Title('Check Gift Card Balance')]
class GiftCardBalance extends Component
{
    public string $code           = '';
    public bool   $checked        = false;
    public ?array $cardInfo       = null;
    public string $errorMessage   = '';

    public function check(GiftCardService $service): void
    {
        $this->validate([
            'code' => 'required|string|min:6|max:30',
        ]);

        $this->errorMessage = '';
        $this->cardInfo     = null;
        $this->checked      = false;

        $card = $service->findUsable(strtoupper(trim($this->code)));

        if (!$card) {
            // Try finding even inactive/expired cards to give better feedback
            $anyCard = \App\Domains\GiftCard\Models\GiftCard::where('code', strtoupper(trim($this->code)))->first();

            if (!$anyCard) {
                $this->errorMessage = 'Gift card not found. Please check the code and try again.';
            } elseif (!$anyCard->is_active) {
                $this->errorMessage = 'This gift card has been deactivated.';
            } elseif ($anyCard->expires_at && $anyCard->expires_at->isPast()) {
                $this->errorMessage = 'This gift card expired on ' . $anyCard->expires_at->format('M d, Y') . '.';
            } elseif ($anyCard->balance <= 0) {
                $this->errorMessage = 'This gift card has a zero balance.';
            } else {
                $this->errorMessage = 'This gift card is no longer valid.';
            }

            $this->checked = true;
            return;
        }

        $this->cardInfo = [
            'code'       => $card->code,
            'balance'    => $card->balance,
            'original'   => $card->initial_balance ?? $card->balance,
            'expires_at' => $card->expires_at ? $card->expires_at->format('M d, Y') : null,
            'is_active'  => $card->is_active,
        ];

        $this->checked = true;
    }

    public function render()
    {
        return view('livewire.storefront.gift-card.balance');
    }
}
