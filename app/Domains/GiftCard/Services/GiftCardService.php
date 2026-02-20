<?php

namespace App\Domains\GiftCard\Services;

use App\Domains\GiftCard\Models\GiftCard;
use Illuminate\Support\Str;

class GiftCardService
{
    /**
     * Generate a unique gift card code.
     */
    public function generateCode(int $length = 16): string
    {
        do {
            // Format: XXXX-XXXX-XXXX-XXXX
            $raw  = strtoupper(Str::random($length));
            $code = implode('-', str_split($raw, 4));
        } while (GiftCard::where('code', $code)->exists());

        return $code;
    }

    /**
     * Issue a new gift card.
     */
    public function issue(float $amount, array $data = []): GiftCard
    {
        return GiftCard::create(array_merge([
            'code'            => $this->generateCode(),
            'initial_balance' => $amount,
            'balance'         => $amount,
            'currency'        => config('app.currency', 'BDT'),
            'is_active'       => true,
        ], $data));
    }

    /**
     * Look up a gift card by code and validate it's usable.
     */
    public function findUsable(string $code): ?GiftCard
    {
        $card = GiftCard::where('code', strtoupper(str_replace('-', '', trim($code))))
            ->orWhere('code', strtoupper(trim($code)))
            ->first();

        return $card?->isUsable() ? $card : null;
    }

    /**
     * Apply gift card to an order, returns the amount deducted.
     */
    public function apply(GiftCard $card, float $orderTotal, int $orderId, ?int $userId = null): float
    {
        $deductAmount = min($card->balance, $orderTotal);
        $card->deduct($deductAmount, $orderId, $userId);

        return $deductAmount;
    }

    /**
     * Reload/top-up an existing gift card.
     */
    public function topUp(GiftCard $card, float $amount, string $note = 'Top-up'): void
    {
        $card->increment('balance', $amount);
        $card->transactions()->create([
            'type'   => 'credit',
            'amount' => $amount,
            'note'   => $note,
        ]);
    }
}
