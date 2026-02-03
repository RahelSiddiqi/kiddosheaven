<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $fillable = ['name', 'show_on_home'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
