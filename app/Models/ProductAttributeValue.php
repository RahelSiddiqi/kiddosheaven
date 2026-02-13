<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttributeValue extends Model
{
    use HasFactory;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    protected $fillable = [
        'product_id',
        'product_attribute_id',
        'value',
        'display_value',
        'color_code',
        'price_modifier',
        'sort_order',
    ];

    /**
     * Get the attribute
     */
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get variant attributes that use this value (for "used in variants" check).
     */
    public function variantAttributes()
    {
        return $this->hasMany(VariantAttribute::class, 'product_attribute_value_id');
    }
}
