<?php

namespace App\Domains\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'name',
        'price',
        'cost_price',
        'compare_at_price',
        'stock_quantity',
        'reserved_quantity',
        'low_stock_alert',
        'weight',
        'length',
        'width',
        'height',
        'image',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variantAttributes(): HasMany
    {
        return $this->hasMany(VariantAttribute::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(\App\Domains\Inventory\Models\InventoryItem::class);
    }

    public function purchaseBatches(): HasMany
    {
        return $this->hasMany(\App\Domains\Inventory\Models\PurchaseBatch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }
}
