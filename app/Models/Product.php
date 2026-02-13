<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'product_type', // simple, variable, digital
        'delivery_type', // instant, schedule, frozen
        'barcode',
        'price', // BDT - Selling Price
        'cost_price', // BDT - Buying Price
        'discount_price',
        'discount_type', // percentage, fixed
        'vat_rate',
        'wholesale_price',
        'profit_margin', // Calculated profit margin percentage
        'sku',
        'stock_quantity',
        'low_stock_alert',
        'stock_status',
        'weight',
        'length',
        'width',
        'height',
        'short_description',
        'description',
        'features',
        'care_instructions',
        'ingredients',
        'safety_warning',
        'images',
        'primary_image',
        'is_featured',
        'is_active',
        'tags',
        'status',
        'brand_id',
        'meta_title',
        'meta_description',
        'video_url',
        'custom_attributes',
        'halal_certified',
        'organic_certified',
        'return_policy',
        'warranty',
        'manufacturer',
    ];

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'custom_attributes' => 'array',
        'halal_certified' => 'boolean',
        'organic_certified' => 'boolean',
        'low_stock_alert' => 'integer',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all variants for this product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the default variant.
     */
    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    /**
     * Get only active variants.
     */
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    /**
     * Get inventory items for this product.
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get the product's wishlist items.
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the product's reviews.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews.
     */
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    /**
     * Calculate average rating.
     */
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    /**
     * Get review count.
     */
    public function getReviewCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get profit per unit (selling price - cost price).
     */
    public function getProfitPerUnitAttribute()
    {
        return $this->price - ($this->cost_price ?? 0);
    }

    /**
     * Get profit margin percentage.
     */
    public function getProfitMarginAttribute()
    {
        if ($this->price <= 0) {
            return 0;
        }
        $cost = $this->cost_price ?? 0;
        return (($this->price - $cost) / $this->price) * 100;
    }

    /**
     * Calculate total profit for a given quantity.
     */
    public function calculateProfit(int $quantity = 1): float
    {
        return $this->profit_per_unit * $quantity;
    }

    /**
     * Get purchase batches for this product.
     */
    public function purchaseBatches(): HasMany
    {
        return $this->hasMany(PurchaseBatch::class);
    }

    /**
     * Get active batches (with remaining stock).
     */
    public function activeBatches()
    {
        return $this->purchaseBatches()->where('remaining_quantity', '>', 0)
            ->whereNotIn('status', ['expired', 'damaged']);
    }

    /**
     * Get total remaining stock from all batches.
     */
    public function getTotalStockAttribute(): int
    {
        return $this->purchaseBatches()->sum('remaining_quantity');
    }

    /**
     * Get stock valuation based on batches.
     */
    public function getStockValuationAttribute(): float
    {
        return $this->purchaseBatches()
            ->where('remaining_quantity', '>', 0)
            ->get()
            ->sum(fn($batch) => $batch->remaining_quantity * $batch->unit_cost);
    }

    /**
     * Get average cost from batches.
     */
    public function getAverageCostAttribute(): float
    {
        $batches = $this->activeBatches()->get();
        if ($batches->isEmpty()) {
            return $this->cost_price ?? 0;
        }
        $totalCost = $batches->sum(fn($b) => $b->remaining_quantity * $b->unit_cost);
        $totalQty = $batches->sum('remaining_quantity');
        return $totalQty > 0 ? $totalCost / $totalQty : 0;
    }

    /**
     * Check if product has stock.
     */
    public function hasStock(): bool
    {
        return $this->total_stock > 0;
    }

    /**
     * Get inventory movements for this product.
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get order items for this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
