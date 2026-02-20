<?php

namespace App\Domains\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Order\Models\Order;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'shipping_rate_id',
        'tracking_number',
        'carrier',
        'carrier_service',
        'status',
        'weight',
        'shipping_cost',
        'tracking_url',
        'notes',
        'shipped_at',
        'delivered_at',
        'estimated_delivery',
    ];

    protected $casts = [
        'shipping_cost'       => 'decimal:2',
        'weight'              => 'decimal:3',
        'shipped_at'          => 'datetime',
        'delivered_at'        => 'datetime',
        'estimated_delivery'  => 'datetime',
    ];

    public const STATUSES = [
        'pending'          => 'Pending',
        'processing'       => 'Processing',
        'picked_up'        => 'Picked Up',
        'in_transit'       => 'In Transit',
        'out_for_delivery' => 'Out for Delivery',
        'delivered'        => 'Delivered',
        'failed'           => 'Failed',
        'returned'         => 'Returned',
    ];

    public const CARRIERS = [
        'steadfast' => 'Steadfast',
        'pathao'    => 'Pathao',
        'redx'      => 'RedX',
        'sundarban' => 'Sundarban Courier',
        'dhl'       => 'DHL',
        'fedex'     => 'FedEx',
        'ups'       => 'UPS',
        'local'     => 'Local Delivery',
        'self'      => 'Self Delivery',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(ShippingRate::class, 'shipping_rate_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function getTrackingLinkAttribute(): ?string
    {
        if ($this->tracking_url) return $this->tracking_url;
        if (!$this->tracking_number) return null;

        return match ($this->carrier) {
            'dhl'   => "https://www.dhl.com/en/express/tracking.html?AWB={$this->tracking_number}",
            'fedex' => "https://www.fedex.com/fedextrack/?trknbr={$this->tracking_number}",
            'ups'   => "https://www.ups.com/track?tracknum={$this->tracking_number}",
            default => null,
        };
    }

    public function markShipped(string $trackingNumber = null, string $carrier = null): void
    {
        $this->update([
            'status'          => 'in_transit',
            'shipped_at'      => now(),
            'tracking_number' => $trackingNumber ?? $this->tracking_number,
            'carrier'         => $carrier ?? $this->carrier,
        ]);

        // Update parent order
        $this->order->update([
            'fulfillment_status'        => 'shipped',
            'shipping_tracking_number'  => $trackingNumber ?? $this->tracking_number,
            'shipping_carrier'          => $carrier ?? $this->carrier,
            'shipped_at'                => now(),
        ]);
    }

    public function markDelivered(): void
    {
        $this->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);

        $this->order->update([
            'fulfillment_status' => 'delivered',
            'delivered_at'       => now(),
            'status'             => 'delivered',
        ]);
    }
}
