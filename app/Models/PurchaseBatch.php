<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'product_id',
        'partner_id',
        'inventory_item_id',
        'unit_cost',
        'total_cost',
        'quantity_received',
        'quantity_remaining',
        'quantity_reserved',
        'supplier_invoice_number',
        'purchase_date',
        'manufacture_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'purchase_date' => 'date',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
        'quantity_received' => 'integer',
        'quantity_remaining' => 'integer',
        'quantity_reserved' => 'integer',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_PARTIALLY_SOLD = 'partially_sold';
    const STATUS_SOLD = 'sold';
    const STATUS_EXPIRED = 'expired';
    const STATUS_DAMAGED = 'damaged';

    /**
     * Boot method to auto-generate batch number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            if (empty($batch->batch_number)) {
                $batch->batch_number = 'BATCH-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
            }
        });
    }

    /**
     * Get the product for this batch.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the partner (supplier) for this batch.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    /**
     * Get the inventory item if this batch is linked to a specific variant.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    /**
     * Get movements for this batch.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'purchase_batch_id');
    }

    /**
     * Calculate remaining value in stock.
     */
    public function getRemainingValueAttribute(): float
    {
        return $this->quantity_remaining * $this->unit_cost;
    }

    /**
     * Calculate sold quantity.
     */
    public function getQuantitySoldAttribute(): int
    {
        return $this->quantity_received - $this->quantity_remaining - $this->quantity_reserved;
    }

    /**
     * Check if batch has available stock.
     */
    public function hasStock(): bool
    {
        return $this->quantity_remaining > 0;
    }

    /**
     * Check if batch is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Get badge class for status.
     */
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

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        return $this->expiry_date->diffInDays(now(), false);
    }

    /**
     * Scope for active batches.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('quantity_remaining', '>', 0);
    }

    /**
     * Scope for batches with stock.
     */
    public function scopeWithStock($query)
    {
        return $query->where('quantity_remaining', '>', 0);
    }

    /**
     * Scope for expiring batches (within days).
     */
    public function scopeExpiringWithinDays($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '>=', now())
                    ->where('expiry_date', '<=', now()->addDays($days));
    }
}
