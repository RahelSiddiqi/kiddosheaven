<?php

namespace App\Domains\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Concerns\BelongsToSite;

class Brand extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'name',
        'slug',
        'description',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(\App\Domains\Product\Models\Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
