<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
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
}
