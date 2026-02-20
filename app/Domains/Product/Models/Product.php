<?php

namespace App\Domains\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Domains\Concerns\BelongsToSite;
use App\Domains\Concerns\HasTags;
use App\Domains\Concerns\HasMetafields;

class Product extends Model
{
    use SoftDeletes;
    use BelongsToSite;
    use HasTags;
    use HasMetafields;

    protected $fillable = [
        'site_id',
        'catalog_id',
        'name',
        'slug',
        'category_id',
        'brand_id',
        'product_type',
        'delivery_type',
        'barcode',
        'price',
        'cost_price',
        'discount_price',
        'discount_type',
        'vat_rate',
        'wholesale_price',
        'profit_margin',
        'sku',
        'stock_quantity',
        'low_stock_alert',
        'stock_status',
        'sold_count',
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
        'video_url',
        'is_featured',
        'is_active',
        'tags',
        'custom_attributes',
        'meta_title',
        'meta_description',
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
        'sold_count' => 'integer',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Catalog\Models\Catalog::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Catalog\Models\Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Catalog\Models\Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(\App\Domains\Inventory\Models\InventoryItem::class);
    }

    public function purchaseBatches(): HasMany
    {
        return $this->hasMany(\App\Domains\Inventory\Models\PurchaseBatch::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(\App\Domains\Inventory\Models\InventoryMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(\App\Domains\Order\Models\OrderItem::class);
    }

    public function wishlist(): HasMany
    {
        return $this->hasMany(\App\Domains\Customer\Models\Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(\App\Domains\Customer\Models\Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(\App\Domains\Customer\Models\Review::class)->where('is_approved', true);
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForCatalog($query, $catalogId)
    {
        return $query->where('catalog_id', $catalogId);
    }

    // ─── Computed Attributes ──────────────────────────────

    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function getProfitPerUnitAttribute()
    {
        return $this->price - ($this->cost_price ?? 0);
    }

    public function getProfitMarginAttribute()
    {
        if ($this->price <= 0) {
            return 0;
        }
        $cost = $this->cost_price ?? 0;
        return (($this->price - $cost) / $this->price) * 100;
    }

    public function calculateProfit(int $quantity = 1): float
    {
        return $this->profit_per_unit * $quantity;
    }

    // ─── Inventory Helpers (preserved) ────────────────────

    public function activeBatches()
    {
        return $this->purchaseBatches()->where('remaining_quantity', '>', 0)
            ->whereNotIn('status', ['expired', 'damaged']);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->purchaseBatches()->sum('remaining_quantity');
    }

    public function getStockValuationAttribute(): float
    {
        return $this->purchaseBatches()
            ->where('remaining_quantity', '>', 0)
            ->get()
            ->sum(fn($batch) => $batch->remaining_quantity * $batch->unit_cost);
    }

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

    public function hasStock(): bool
    {
        return $this->total_stock > 0;
    }

    public function getImagePathAttribute(): ?string
    {
        return $this->primary_image ?? ($this->images[0] ?? null);
    }
}
