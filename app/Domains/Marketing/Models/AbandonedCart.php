<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Concerns\BelongsToSite;

/**
 * Tracks shopping carts that were started but never converted to orders.
 * Used for abandoned cart recovery email sequences.
 */
class AbandonedCart extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'user_id',        // nullable if guest
        'email',          // captured from checkout step 1
        'phone',
        'token',          // unique cart token for recovery URL
        'items',          // JSON: [{product_id, variant_id, qty, price, name}]
        'subtotal',
        'currency',
        'recovery_url',
        'status',         // active | recovered | expired
        'reminder_1_sent_at',
        'reminder_2_sent_at',
        'reminder_3_sent_at',
        'recovered_at',
        'recovered_order_id',
        'expires_at',
    ];

    protected $casts = [
        'items'              => 'array',
        'subtotal'           => 'decimal:2',
        'reminder_1_sent_at' => 'datetime',
        'reminder_2_sent_at' => 'datetime',
        'reminder_3_sent_at' => 'datetime',
        'recovered_at'       => 'datetime',
        'expires_at'         => 'datetime',
    ];

    const STATUS_ACTIVE    = 'active';
    const STATUS_RECOVERED = 'recovered';
    const STATUS_EXPIRED   = 'expired';

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function recoveredOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Order\Models\Order::class, 'recovered_order_id');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeNeedsReminder($query, int $reminderNumber)
    {
        $column       = "reminder_{$reminderNumber}_sent_at";
        $hoursAgo     = match ($reminderNumber) {
            1 => 1,   // 1 hour after abandonment
            2 => 24,  // 24 hours
            3 => 72,  // 3 days
            default => 1,
        };

        return $query->active()
            ->whereNull($column)
            ->whereNotNull('email')
            ->where('created_at', '<=', now()->subHours($hoursAgo));
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function markRecovered(int $orderId): void
    {
        $this->update([
            'status'              => self::STATUS_RECOVERED,
            'recovered_at'        => now(),
            'recovered_order_id'  => $orderId,
        ]);
    }

    public function itemCount(): int
    {
        return array_sum(array_column($this->items ?? [], 'quantity'));
    }
}
