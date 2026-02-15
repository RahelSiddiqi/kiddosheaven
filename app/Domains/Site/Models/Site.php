<?php

namespace App\Domains\Site\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Site extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'is_active',
        'is_default',
        'locale',
        'currency',
        'timezone',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'settings' => 'array',
    ];

    // ─── Relationships ────────────────────────────────────

    public function catalogs(): HasMany
    {
        return $this->hasMany(\App\Domains\Catalog\Models\Catalog::class);
    }

    public function themeSetting(): HasOne
    {
        return $this->hasOne(SiteThemeSetting::class);
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ─── Helpers ──────────────────────────────────────────

    public function getDefaultCatalog()
    {
        return $this->catalogs()->where('is_default', true)->first()
            ?? $this->catalogs()->where('is_active', true)->first();
    }

    public function getThemeConfig(): array
    {
        return $this->themeSetting?->toThemeArray() ?? [];
    }
}
