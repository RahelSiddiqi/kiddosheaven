<?php

namespace App\Domains\GiftCard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Concerns\BelongsToSite;

class GiftCard extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'code',
        'initial_balance',
        'balance',
        'currency',
        'is_active',
        'expires_at',
        'issued_to_user_id',   // nullable — can be anonymous
        'issued_to_email',
        'purchased_by_user_id',
        'order_id',            // order that generated this gift card
        'note',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'balance'         => 'decimal:2',
        'is_active'       => 'boolean',
        'expires_at'      => 'datetime',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function transactions(): HasMany
    {
        return $this->hasMany(GiftCardTransaction::class);
    }

    public function issuedTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'issued_to_user_id');
    }

    public function purchasedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'purchased_by_user_id');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where('balance', '>', 0);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isUsable(): bool
    {
        return $this->is_active && !$this->isExpired() && $this->balance > 0;
    }

    public function usableBalance(): float
    {
        return $this->isUsable() ? (float) $this->balance : 0.0;
    }

    /**
     * Deduct amount from balance, create transaction.
     */
    public function deduct(float $amount, int $orderId, ?int $userId = null): GiftCardTransaction
    {
        $deduct = min($amount, $this->balance);
        $this->decrement('balance', $deduct);

        return $this->transactions()->create([
            'type'    => 'debit',
            'amount'  => $deduct,
            'order_id' => $orderId,
            'user_id'  => $userId,
            'note'    => "Applied to order #{$orderId}",
        ]);
    }
}
