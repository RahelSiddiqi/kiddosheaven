<?php

namespace App\Domains\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Platform subscription (SaaS merchant billing) via Stripe Cashier.
 * Tracks which Plan a merchant/site is subscribed to.
 */
class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'type',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'quantity',
        'trial_ends_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at'       => 'datetime',
        'quantity'      => 'integer',
    ];

    const STATUS_ACTIVE            = 'active';
    const STATUS_TRIALING          = 'trialing';
    const STATUS_PAST_DUE          = 'past_due';
    const STATUS_PAUSED            = 'paused';
    const STATUS_CANCELED          = 'canceled';
    const STATUS_INCOMPLETE        = 'incomplete';
    const STATUS_INCOMPLETE_EXPIRED = 'incomplete_expired';

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->whereIn('stripe_status', [self::STATUS_ACTIVE, self::STATUS_TRIALING]);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function isActive(): bool
    {
        return in_array($this->stripe_status, [self::STATUS_ACTIVE, self::STATUS_TRIALING]);
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function onGracePeriod(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture() && !$this->isActive();
    }
}
