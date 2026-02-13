<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'purchase_batch_id',
        'quantity_on_hand',
        'quantity_reserved',
        'location',
        'bin_number',
        'aisle',
        'shelf',
        'unit_cost',
    ];

    protected $casts = [
        'quantity_on_hand' => 'integer',
        'quantity_reserved' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    protected $appends = ['quantity_available', 'total_value'];

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the purchase batch.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(PurchaseBatch::class, 'purchase_batch_id');
    }

    /**
     * Get available quantity.
     */
    public function getQuantityAvailableAttribute(): int
    {
        return max(0, $this->quantity_on_hand - $this->quantity_reserved);
    }

    /**
     * Get total value.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->quantity_on_hand * $this->unit_cost;
    }

    /**
     * Reserve quantity.
     */
    public function reserve(int $quantity): bool
    {
        if ($this->quantity_available < $quantity) {
            return false;
        }

        $this->increment('quantity_reserved', $quantity);
        return true;
    }

    /**
     * Release reserved quantity.
     */
    public function release(int $quantity): void
    {
        $this->decrement('quantity_reserved', min($quantity, $this->quantity_reserved));
    }

    /**
     * Deduct quantity.
     */
    public function deduct(int $quantity): bool
    {
        if ($this->quantity_on_hand < $quantity) {
            return false;
        }

        $this->decrement('quantity_on_hand', $quantity);
        $this->decrement('quantity_reserved', min($quantity, $this->quantity_reserved));
        return true;
    }
}
