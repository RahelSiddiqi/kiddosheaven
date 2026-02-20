<?php

namespace App\Domains\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Concerns\BelongsToSite;

/**
 * Draft orders allow admins to create orders on behalf of customers
 * before sending an invoice link for payment.  Mirrors Shopify Draft Orders.
 */
class DraftOrder extends Model
{
    use BelongsToSite;
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'draft_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'billing_address',
        'shipping_address',
        'line_items',          // JSON: [{product_id, variant_id, qty, price, title, sku}]
        'subtotal',
        'discount_amount',
        'coupon_code',
        'shipping_amount',
        'tax_amount',
        'total_amount',
        'currency',
        'note',
        'tags',
        'payment_method',
        'status',          // open | invoice_sent | completed | cancelled
        'invoice_url',
        'invoice_sent_at',
        'completed_order_id',
        'expires_at',
    ];

    protected $casts = [
        'line_items'       => 'array',
        'billing_address'  => 'array',
        'shipping_address' => 'array',
        'subtotal'         => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'shipping_amount'  => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'invoice_sent_at'  => 'datetime',
        'expires_at'       => 'datetime',
    ];

    const STATUS_OPEN         = 'open';
    const STATUS_INVOICE_SENT = 'invoice_sent';
    const STATUS_COMPLETED    = 'completed';
    const STATUS_CANCELLED    = 'cancelled';

    // ---------------------------------------------------------------
    // Booting
    // ---------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (DraftOrder $draft) {
            if (empty($draft->draft_number)) {
                $draft->draft_number = '#D' . str_pad(
                    (DraftOrder::max('id') ?? 0) + 1,
                    6, '0', STR_PAD_LEFT
                );
            }
        });
    }

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function completedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'completed_order_id');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_INVOICE_SENT]);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function itemCount(): int
    {
        return array_sum(array_column($this->line_items ?? [], 'qty'));
    }
}
