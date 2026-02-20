<?php

namespace App\Domains\Site\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'stripe_price_id', 'billing_period',
        'price_cents', 'features', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features'  => 'array',
        'is_active' => 'boolean',
    ];

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return isset($features[$feature]) && $features[$feature] !== false && $features[$feature] !== 0;
    }

    public function getFeatureValue(string $feature, mixed $default = null): mixed
    {
        return ($this->features ?? [])[$feature] ?? $default;
    }
}
