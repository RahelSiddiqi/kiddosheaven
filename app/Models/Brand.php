<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
