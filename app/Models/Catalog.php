<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $fillable = ['name', 'type', 'description', 'icon', 'show_on_home'];

    protected $casts = [
        'show_on_home' => 'boolean',
    ];

    /**
     * Get the products for this catalog.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the attributes for this catalog.
     */
    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'catalog_attributes')
            ->withPivot(['sort_order', 'is_required'])
            ->orderByPivot('sort_order');
    }

    /**
     * Get catalog type options.
     */
    public static function getTypeOptions(): array
    {
        return [
            'general' => 'General',
            'grocery' => 'Grocery',
            'clothing' => 'Clothing & Apparel',
            'toys' => 'Toys & Games',
            'food' => 'Food & Beverages',
            'electronics' => 'Electronics',
            'home' => 'Home & Garden',
            'beauty' => 'Beauty & Personal Care',
            'sports' => 'Sports & Outdoors',
            'books' => 'Books & Media',
            'baby' => 'Baby Products',
            'health' => 'Health & Wellness',
        ];
    }
}
