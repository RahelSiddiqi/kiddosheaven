<?php

namespace App\Domains\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $fillable = [
        'name',
        'countries',
        'regions',
        'is_rest_of_world',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'countries'       => 'array',
        'regions'         => 'array',
        'is_rest_of_world'=> 'boolean',
        'is_active'       => 'boolean',
    ];

    public function rates(): HasMany
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function activeRates(): HasMany
    {
        return $this->hasMany(ShippingRate::class)->where('is_active', true);
    }

    /** Find the zone that covers a given country code. */
    public static function forCountry(string $countryCode): ?self
    {
        return static::where('is_active', true)
            ->where(function ($q) use ($countryCode) {
                $q->whereJsonContains('countries', $countryCode)
                  ->orWhere('is_rest_of_world', true);
            })
            ->orderByRaw('is_rest_of_world ASC') // prefer specific zones over rest-of-world
            ->first();
    }
}
