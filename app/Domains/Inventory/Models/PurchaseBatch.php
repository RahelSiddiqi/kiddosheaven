<?php

namespace App\Domains\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseBatch extends Model
{
    protected $fillable = [
        'batch_number',
        'product_id',
        'product_variant_id',
        'partner_id',
        'unit_cost',
        'quantity_received',
        'remaining_quantity',
        'quantity_reserved',
        'status',
        'supplier',
        'supplier_invoice_number',
        'purchase_date',
        'manufacture_date',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'purchase_date' => 'date',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
        'quantity_received' => 'integer',
        'remaining_quantity' => 'integer',
        'quantity_reserved' => 'integer',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_PARTIALLY_SOLD = 'partially_sold';
    const STATUS_SOLD = 'sold';
    const STATUS_EXPIRED = 'expired';
    const STATUS_DAMAGED = 'damaged';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            if (empty($batch->batch_number)) {
                $batch->batch_number = 'BATCH-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Product\Models\Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Product\Models\ProductVariant::class, 'product_variant_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Accounting\Models\Partner::class, 'partner_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'batch_id');
    }

    public function getRemainingValueAttribute(): float
    {
        return $this->remaining_quantity * $this->unit_cost;
    }

    public function getQuantitySoldAttribute(): int
    {
        return $this->quantity_received - $this->remaining_quantity - $this->quantity_reserved;
    }

    public function hasStock(): bool
    {
        return $this->remaining_quantity > 0;
    }

    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->remaining_quantity - $this->quantity_reserved);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::STATUS_PARTIALLY_SOLD => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
            self::STATUS_SOLD => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            self::STATUS_EXPIRED => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            self::STATUS_DAMAGED => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        return $this->expiry_date->diffInDays(now(), false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('remaining_quantity', '>', 0);
    }

    public function scopeWithStock($query)
    {
        return $query->where('remaining_quantity', '>', 0);
    }

    public function scopeExpiringWithinDays($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '>=', now())
                    ->where('expiry_date', '<=', now()->addDays($days));
    }
}
