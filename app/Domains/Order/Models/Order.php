<?php

namespace App\Domains\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Concerns\BelongsToSite;
use App\Domains\Concerns\HasTags;

class Order extends Model
{
    use BelongsToSite;
    use HasTags;

    protected $fillable = [
        'site_id',
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address_line',
        'city',
        'postal_code',
        'notes',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'status',
        'status_notes',
        'cancellation_reason',
        // Coupon
        'coupon_id',
        'coupon_code',
        // Fulfillment
        'fulfillment_status',
        'shipping_carrier',
        'shipping_tracking_number',
        'shipping_method_name',
        'shipped_at',
        'delivered_at',
        // Refunds
        'refunded_amount',
        'refund_reason',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'shipped_at'      => 'datetime',
        'delivered_at'    => 'datetime',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Customer\Models\Address::class, 'address_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Marketing\Models\Coupon::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(\App\Domains\Shipping\Models\Shipment::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(\App\Domains\Returns\Models\ReturnRequest::class);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function isFulfilled(): bool
    {
        return in_array($this->fulfillment_status, ['shipped', 'delivered']);
    }

    public function isDelivered(): bool
    {
        return $this->fulfillment_status === 'delivered';
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format((float) $this->total_amount, 2);
    }
}
