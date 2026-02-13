<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

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
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
    ];

    protected $appends = ['available_quantity', 'is_low_stock', 'is_in_stock'];

    /**
     * Get the product that owns this variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute values for this variant.
     * E.g., Color: Red, Size: Large, Weight: 1kg
     */
    public function variantAttributes(): HasMany
    {
        return $this->hasMany(VariantAttribute::class, 'product_variant_id');
    }

    /**
     * Get the product attributes through variant attributes.
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttribute::class,
            'variant_attributes',
            'product_variant_id',
            'product_attribute_id'
        )->withPivot('product_attribute_value_id');
    }

    /**
     * Get purchase batches for this variant.
     */
    public function purchaseBatches(): HasMany
    {
        return $this->hasMany(PurchaseBatch::class, 'product_variant_id');
    }

    /**
     * Get inventory items (variant in specific location/batch).
     */
    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'product_variant_id');
    }

    /**
     * Get inventory movements for this variant.
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'product_variant_id');
    }

    /**
     * Get order items that used this variant.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    /**
     * Get available quantity (not reserved).
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }

    /**
     * Check if variant is low on stock.
     */
    public function getIsLowStockAttribute(): bool
    {
        if (!$this->low_stock_alert) {
            return false;
        }
        return $this->available_quantity <= $this->low_stock_alert;
    }

    /**
     * Check if variant is in stock.
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->available_quantity > 0;
    }

    /**
     * Get the full variant name with attributes.
     * E.g., "Red T-Shirt - Large - 1kg"
     */
    public function getFullNameAttribute(): string
    {
        if ($this->name) {
            return $this->name;
        }

        $attributeNames = $this->variantAttributes()
            ->with('attributeValue')
            ->get()
            ->map(fn($va) => $va->attributeValue->value)
            ->implode(' - ');

        return $this->product->name . ($attributeNames ? " - {$attributeNames}" : '');
    }

    /**
     * Calculate weighted average cost from batches.
     */
    public function calculateAverageCost(): float
    {
        $batches = $this->purchaseBatches()
            ->where('remaining_quantity', '>', 0)
            ->get();

        if ($batches->isEmpty()) {
            return $this->cost_price ?? 0;
        }

        $totalCost = $batches->sum(fn($b) => $b->unit_cost * $b->remaining_quantity);
        $totalQty = $batches->sum('remaining_quantity');

        return $totalQty > 0 ? round($totalCost / $totalQty, 2) : 0;
    }

    /**
     * Update cost price to weighted average.
     */
    public function updateAverageCost(): void
    {
        $this->cost_price = $this->calculateAverageCost();
        $this->save();
    }

    /**
     * Reserve stock for an order.
     */
    public function reserve(int $quantity): bool
    {
        if ($this->available_quantity < $quantity) {
            return false;
        }

        $this->increment('reserved_quantity', $quantity);
        return true;
    }

    /**
     * Release reserved stock (e.g., order cancelled).
     */
    public function release(int $quantity): void
    {
        $this->decrement('reserved_quantity', min($quantity, $this->reserved_quantity));
    }

    /**
     * Deduct stock when order is fulfilled.
     */
    public function deduct(int $quantity): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);
        $this->decrement('reserved_quantity', min($quantity, $this->reserved_quantity));
        return true;
    }

    /**
     * Add stock when receiving inventory.
     */
    public function addStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
    }

    /**
     * Scope to get only active variants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get in-stock variants.
     */
    public function scopeInStock($query)
    {
        return $query->whereRaw('stock_quantity - reserved_quantity > 0');
    }

    /**
     * Scope to get low stock variants.
     */
    public function scopeLowStock($query)
    {
        return $query->whereNotNull('low_stock_alert')
            ->whereRaw('stock_quantity - reserved_quantity <= low_stock_alert');
    }

    /**
     * Boot method.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate SKU if not provided
        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-' . strtoupper(uniqid());
            }
        });
    }
}
