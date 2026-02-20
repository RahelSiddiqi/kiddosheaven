<?php

namespace App\Domains\Site\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Site extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'subdomain',
        'custom_domain',
        'domain_verified_at',
        'domain_txt_record',
        'plan_id',
        'owner_id',
        'trial_ends_at',
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
        'trial_ends_at' => 'datetime',
        'domain_verified_at' => 'datetime',
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

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function webhookSubscriptions(): HasMany
    {
        return $this->hasMany(WebhookSubscription::class);
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

    /**
     * Check if the custom domain has been verified via DNS TXT record.
     */
    public function isDomainVerified(): bool
    {
        return $this->custom_domain !== null && $this->domain_verified_at !== null;
    }
}
