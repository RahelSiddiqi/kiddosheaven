<?php

namespace App\Domains\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Catalog extends Model
{

    protected $fillable = [
        'site_id',
        'name',
        'slug',
        'description',
        'type',
        'target_audience',
        'region_codes',
        'currency',
        'language',
        'is_active',
        'is_default',
        'sort_order',
        'settings',
        'pricing_rules',
    ];

    protected $casts = [
        'region_codes' => 'array',
        'settings' => 'array',
        'pricing_rules' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────

    public function products(): HasMany
    {
        return $this->hasMany(\App\Domains\Product\Models\Product::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(\App\Domains\Catalog\Models\Category::class);
    }

    public function site()
    {
        return $this->belongsTo(\App\Domains\Site\Models\Site::class);
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeB2B($query)
    {
        return $query->where('type', 'b2b');
    }

    public function scopeB2C($query)
    {
        return $query->where('type', 'b2c');
    }

    public function scopeRegional($query, ?string $regionCode = null)
    {
        $query = $query->where('type', 'regional');

        if ($regionCode) {
            $query->whereJsonContains('region_codes', $regionCode);
        }

        return $query;
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ─── Helpers ──────────────────────────────────────────

    public function isB2B(): bool
    {
        return $this->type === 'b2b';
    }

    public function isB2C(): bool
    {
        return $this->type === 'b2c';
    }

    public function isRegional(): bool
    {
        return $this->type === 'regional';
    }

    public function getActiveProductsCount(): int
    {
        return $this->products()->where('is_active', true)->count();
    }

    public function applyPricingRules(float $basePrice): float
    {
        if (!$this->pricing_rules) {
            return $basePrice;
        }

        $discount = $this->pricing_rules['discount_percentage'] ?? 0;

        return round($basePrice * (1 - $discount / 100), 2);
    }

    public function getTieredPrice(float $basePrice, int $quantity): float
    {
        if (!$this->pricing_rules || empty($this->pricing_rules['tiered_pricing'])) {
            return $this->applyPricingRules($basePrice);
        }

        $tiers = collect($this->pricing_rules['tiered_pricing'])
            ->sortByDesc('min_qty');

        foreach ($tiers as $tier) {
            if ($quantity >= $tier['min_qty']) {
                return round($basePrice * (1 - $tier['discount'] / 100), 2);
            }
        }

        return $this->applyPricingRules($basePrice);
    }
}
