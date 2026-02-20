<?php

namespace App\Domains\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Concerns\BelongsToSite;

/**
 * Tracks individual payment transactions for orders.
 * One order can have multiple transactions (retry, partial, refund).
 */
class PaymentTransaction extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'order_id',
        'gateway_code',
        'transaction_id',      // gateway's transaction ID
        'gateway_reference',   // additional gateway reference
        'type',                // charge | refund | partial_refund | void
        'status',              // pending | success | failed | cancelled | refunded
        'amount',
        'currency',
        'fee_amount',          // gateway fee
        'net_amount',          // amount - fee
        'payment_method_type', // card | mobile_banking | internet_banking | wallet
        'last_four',           // last 4 digits for card
        'card_brand',
        'phone',               // for bKash/Nagad
        'raw_response',        // JSON gateway response
        'error_message',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'fee_amount'   => 'decimal:2',
        'net_amount'   => 'decimal:2',
        'raw_response' => 'array',
        'paid_at'      => 'datetime',
        'refunded_at'  => 'datetime',
    ];

    const STATUS_PENDING   = 'pending';
    const STATUS_SUCCESS   = 'success';
    const STATUS_FAILED    = 'failed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED  = 'refunded';

    const TYPE_CHARGE         = 'charge';
    const TYPE_REFUND         = 'refund';
    const TYPE_PARTIAL_REFUND = 'partial_refund';
    const TYPE_VOID           = 'void';

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Order\Models\Order::class);
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS)->where('type', self::TYPE_CHARGE);
    }

    public function scopeRefunds($query)
    {
        return $query->whereIn('type', [self::TYPE_REFUND, self::TYPE_PARTIAL_REFUND]);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isRefund(): bool
    {
        return in_array($this->type, [self::TYPE_REFUND, self::TYPE_PARTIAL_REFUND]);
    }
}
