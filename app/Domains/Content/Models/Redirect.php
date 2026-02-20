<?php

namespace App\Domains\Content\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domains\Concerns\BelongsToSite;

/**
 * URL redirect rules for SEO.
 * When a product/page URL changes, create a 301 redirect to avoid broken links.
 * Shopify has this feature — supports 301 (permanent) and 302 (temporary).
 */
class Redirect extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'from_path',   // e.g. /old-product-name
        'to_path',     // e.g. /products/new-product-name or https://...
        'type',        // 301 | 302
        'is_active',
        'hit_count',
        'note',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'type'      => 'integer',
        'hit_count' => 'integer',
    ];

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function incrementHit(): void
    {
        $this->increment('hit_count');
    }

    public function isPermanent(): bool
    {
        return $this->type === 301;
    }
}
