<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Concerns\BelongsToSite;

class Coupon extends Model
{
    use HasFactory;
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'code',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'status',
        'is_general',
        'user_id',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_general' => 'boolean',
    ];

    /**
     * Get the user that owns the coupon (for user-specific coupons).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if coupon is valid.
     */
    public function isValid(User $user = null): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if (!$this->is_general && $user) {
            if ($this->user_id !== $user->id) {
                return false;
            }
        }

        if ($this->valid_from && now()->isBefore($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && now()->isAfter($this->valid_until)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount(float $orderTotal, User $user = null): float
    {
        if (!$this->isValid($user)) {
            return 0;
        }

        if ($this->min_order_amount && $orderTotal < $this->min_order_amount) {
            return 0;
        }

        return match ($this->type) {
            'percentage' => min(($orderTotal * $this->value) / 100, $this->max_discount ?? $orderTotal),
            'fixed' => min($this->value, $orderTotal),
            'shipping' => 0, // Shipping discount handled separately
            default => 0,
        };
    }
}
