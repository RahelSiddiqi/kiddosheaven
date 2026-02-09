<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CatalogType extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($catalogType) {
            if (empty($catalogType->slug)) {
                $catalogType->slug = Str::slug($catalogType->name);
            }
        });

        static::updating(function ($catalogType) {
            if (empty($catalogType->slug) || $catalogType->isDirty('name')) {
                $catalogType->slug = Str::slug($catalogType->name);
            }
        });
    }

    /**
     * Get the catalogs for this type.
     */
    public function catalogs()
    {
        return $this->hasMany(Catalog::class, 'type', 'slug');
    }

    /**
     * Get the attributes available for this catalog type.
     */
    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'catalog_type_attributes', 'catalog_type_id', 'attribute_id')
            ->withPivot(['sort_order', 'is_required'])
            ->orderByPivot('sort_order');
    }

    /**
     * Get attributes that are enabled for a specific catalog.
     * This merges type attributes with catalog-specific selections.
     */
    public function getEnabledAttributesForCatalog($catalogId)
    {
        // Get attributes from catalog_type_attributes
        $typeAttrs = $this->attributes()->get();

        // Get attributes specifically enabled for this catalog
        $catalogAttrs = Catalog::find($catalogId)->attributes()->pluck('product_attributes.id')->toArray();

        // If catalog has specific selections, return only those
        if (!empty($catalogAttrs)) {
            return $typeAttrs->whereIn('id', $catalogAttrs);
        }

        // Otherwise return all type attributes
        return $typeAttrs;
    }

    /**
     * Get active types ordered by sort order.
     */
    public static function getActiveOptions(): array
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name', 'slug')
            ->toArray();
    }

    /**
     * Get all types for dropdowns.
     */
    public static function getAllOptions(): array
    {
        return static::orderBy('sort_order')
            ->pluck('name', 'slug')
            ->toArray();
    }
}
