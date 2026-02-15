<?php

namespace App\Domains\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PricingTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'strategy_type',
        'config',
        'is_active',
        'is_global',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_pricing_template');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
