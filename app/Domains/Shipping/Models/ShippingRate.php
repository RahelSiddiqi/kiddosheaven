<?php

namespace App\Domains\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    protected $fillable = [
        'shipping_zone_id',
        'name',
        'type',
        'price',
        'free_above_amount',
        'min_order_amount',
        'max_order_amount',
        'min_weight',
        'max_weight',
        'carrier',
        'estimated_days_min',
        'estimated_days_max',
        'is_active',
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'free_above_amount' => 'decimal:2',
        'min_order_amount'  => 'decimal:2',
        'max_order_amount'  => 'decimal:2',
        'min_weight'        => 'decimal:3',
        'max_weight'        => 'decimal:3',
        'is_active'         => 'boolean',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    /**
     * Calculate effective shipping cost for a given order amount and weight.
     */
    public function calculateCost(float $orderAmount, float $weight = 0): float
    {
        // Free shipping threshold
        if ($this->free_above_amount && $orderAmount >= $this->free_above_amount) {
            return 0.0;
        }

        return match ($this->type) {
            'free'         => 0.0,
            'flat'         => (float) $this->price,
            'weight_based' => (float) $this->price * $weight,
            default        => (float) $this->price,
        };
    }

    public function getEstimatedDeliveryLabel(): string
    {
        if ($this->estimated_days_min && $this->estimated_days_max) {
            if ($this->estimated_days_min === $this->estimated_days_max) {
                return $this->estimated_days_min . ' business day(s)';
            }
            return $this->estimated_days_min . '–' . $this->estimated_days_max . ' business days';
        }
        return 'Estimated delivery varies';
    }
}
