<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'catalog_id',
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
        'images',
        'primary_image',
        'is_featured',
        'tags',
        'status',
        'brand_id',
        'meta_title',
        'meta_description',
        'video_url',
        'custom_attributes',
        'variants',
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
        'custom_attributes' => 'array',
        'variants' => 'array',
        'halal_certified' => 'boolean',
        'organic_certified' => 'boolean',
        'low_stock_alert' => 'integer',
    ];
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
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
}
