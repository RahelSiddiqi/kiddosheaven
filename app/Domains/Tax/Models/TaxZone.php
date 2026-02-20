<?php

namespace App\Domains\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Concerns\BelongsToSite;

/**
 * A tax zone groups geographic regions (countries, states) that share the same tax rules.
 * Shopify: Tax regions.
 */
class TaxZone extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'name',         // e.g. "Bangladesh", "International", "EU VAT"
        'countries',    // JSON: ["BD", "IN"] — ISO3166 codes
        'is_default',   // fall-through zone if no other matches
        'is_active',
    ];

    protected $casts = [
        'countries'  => 'array',
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function rates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Find the zone that applies to a given country code.
     */
    public static function forCountry(string $countryCode): ?static
    {
        return static::active()
            ->get()
            ->first(fn ($zone) => in_array(strtoupper($countryCode), $zone->countries ?? []))
            ?? static::active()->where('is_default', true)->first();
    }
}
