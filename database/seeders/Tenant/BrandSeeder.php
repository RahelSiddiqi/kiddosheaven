<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Domains\Catalog\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::create([
            'name' => 'Fisher-Price',
            'slug' => 'fisher-price',
            'description' => 'Leading toy manufacturer',
            'is_active' => true,
        ]);

        Brand::create([
            'name' => 'LEGO',
            'slug' => 'lego',
            'description' => 'Building blocks and construction toys',
            'is_active' => true,
        ]);

        Brand::create([
            'name' => 'Mattel',
            'slug' => 'mattel',
            'description' => 'Toys and family products',
            'is_active' => true,
        ]);

        Brand::create([
            'name' => 'Hasbro',
            'slug' => 'hasbro',
            'description' => 'Games and toys',
            'is_active' => true,
        ]);

        Brand::create([
            'name' => 'Carter\'s',
            'slug' => 'carters',
            'description' => 'Children\'s clothing',
            'is_active' => true,
        ]);
    }
}
