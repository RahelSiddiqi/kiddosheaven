<?php

namespace App\Domains\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Concerns\BelongsToSite;

class Collection extends Model
{
    use BelongsToSite, SoftDeletes;

    protected $fillable = [
        'site_id',
        'name',
        'slug',
        'description',
        'image_path',
        'type',
        'conditions',
        'condition_match',
        'sort_order',
        'is_active',
        'is_featured',
        'position',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'conditions'  => 'array',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Domains\Product\Models\Product::class,
            'collection_products'
        )->withPivot('position')->orderByPivot('position');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function isAutomatic(): bool
    {
        return $this->type === 'automatic';
    }

    public function isManual(): bool
    {
        return $this->type === 'manual';
    }
}
