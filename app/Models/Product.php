<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'catalog_id',
        'price',
        'short_description',
        'description',
        'images',
        'primary_image',
        'is_featured',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }
}
