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
     * This returns attributes from the catalog's type that are enabled for this catalog.
     */
    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'catalog_attributes')
            ->withPivot(['sort_order', 'is_required'])
            ->orderByPivot('sort_order');
    }

    /**
     * Get all available attributes from the catalog's type.
     * These are attributes that CAN be enabled for this catalog.
     */
    public function availableAttributes()
    {
        if (!$this->type) {
            return collect();
        }

        return $this->type->attributes;
    }

    /**
     * Get the enabled attribute IDs for this catalog.
     */
    public function getEnabledAttributeIdsAttribute()
    {
        return $this->attributes()->pluck('product_attributes.id')->toArray();
    }

    /**
     * Check if an attribute is available for this catalog (from its type).
     */
    public function canUseAttribute($attributeId)
    {
        if (!$this->type) {
            return false;
        }

        return $this->type->attributes()->where('product_attributes.id', $attributeId)->exists();
    }

    /**
     * Get catalog type options from database.
     */
    public static function getTypeOptions(): array
    {
        return CatalogType::getActiveOptions();
    }

    /**
     * Get the catalog type.
     */
    public function type()
    {
        return $this->belongsTo(CatalogType::class, 'type', 'slug');
    }
}
