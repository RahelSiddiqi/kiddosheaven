<?php

namespace App\Domains\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Concerns\BelongsToSite;

/**
 * Represents a payment gateway configuration per site.
 * Supports: cod, stripe, bkash, nagad, sslcommerz, paypal, etc.
 */
class PaymentGateway extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'name',          // display name: "bKash", "Nagad", etc.
        'code',          // slug: bkash, nagad, stripe, cod, sslcommerz, paypal
        'is_active',
        'is_test_mode',
        'sort_order',
        'config',        // JSON: API keys, merchant ID, etc. (encrypted at rest)
        'icon',
        'description',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'is_test_mode' => 'boolean',
        'config'       => 'encrypted:array',
        'sort_order'   => 'integer',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class, 'gateway_code', 'code');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function configValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }
}
