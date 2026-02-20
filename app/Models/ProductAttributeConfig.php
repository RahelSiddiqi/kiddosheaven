<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeConfig extends Model
{
    protected $fillable = [
        'product_id',
        'product_attribute_id',
        'usage_type',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    const USAGE_VARIANT = 'variant';
    const USAGE_SPECIFICATION = 'specification';

    /**
     * Get the product that owns this config.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attribute that this config is for.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }
}
