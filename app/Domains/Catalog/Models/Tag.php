<?php

namespace App\Domains\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use App\Domains\Concerns\BelongsToSite;

/**
 * Polymorphic tag system — attach tags to products, orders, customers, etc.
 * Inspired by Shopify's tagging system.
 */
class Tag extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'name',
        'slug',
        'type',   // product | order | customer | collection | general
        'color',  // optional UI color
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function products(): MorphToMany
    {
        return $this->morphedByMany(\App\Domains\Product\Models\Product::class, 'taggable');
    }

    public function orders(): MorphToMany
    {
        return $this->morphedByMany(\App\Domains\Order\Models\Order::class, 'taggable');
    }

    public function customers(): MorphToMany
    {
        return $this->morphedByMany(\App\Models\User::class, 'taggable');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeForType($query, string $type)
    {
        return $query->whereIn('type', [$type, 'general']);
    }

    // ---------------------------------------------------------------
    // Booting
    // ---------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Tag $tag) {
            if (empty($tag->slug)) {
                $tag->slug = \Illuminate\Support\Str::slug($tag->name);
            }
        });
    }
}
