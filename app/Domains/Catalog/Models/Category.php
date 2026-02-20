<?php

namespace App\Domains\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Concerns\BelongsToSite;

class Category extends Model
{
    use SoftDeletes;
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'catalog_id',
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'parent_id',
        'show_on_home',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'show_on_home' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(\App\Domains\Product\Models\Product::class, 'category_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(
            \App\Domains\Product\Models\ProductAttribute::class,
            'category_attributes'
        )->withPivot(['sort_order', 'is_required'])->orderByPivot('sort_order');
    }

    public function pricingTemplates(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Domains\Catalog\Models\PricingTemplate::class,
            'category_pricing_template'
        );
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeShowOnHome($query)
    {
        return $query->where('show_on_home', true);
    }

    public function scopeForCatalog($query, $catalogId)
    {
        return $query->where('catalog_id', $catalogId);
    }

    // ─── Helpers ──────────────────────────────────────────

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    public function getFullNameAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_name . ' > ' . $this->name;
        }
        return $this->name;
    }

    public function ancestors()
    {
        $ancestors = collect();
        $category = $this->parent;

        while ($category) {
            $ancestors->push($category);
            $category = $category->parent;
        }

        return $ancestors->reverse();
    }

    public function descendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }
}
