<?php

namespace App\Domains\Inventory\Models;

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

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Product\Models\Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Product\Models\ProductVariant::class, 'product_variant_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PurchaseBatch::class, 'purchase_batch_id');
    }

    public function getQuantityAvailableAttribute(): int
    {
        return max(0, $this->quantity_on_hand - $this->quantity_reserved);
    }

    public function getTotalValueAttribute(): float
    {
        return $this->quantity_on_hand * $this->unit_cost;
    }

    public function reserve(int $quantity): bool
    {
        if ($this->quantity_available < $quantity) {
            return false;
        }

        $this->increment('quantity_reserved', $quantity);
        return true;
    }

    public function release(int $quantity): void
    {
        $this->decrement('quantity_reserved', min($quantity, $this->quantity_reserved));
    }

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
