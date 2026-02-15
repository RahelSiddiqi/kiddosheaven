<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\Catalog;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $catalog = Catalog::where('is_default', true)->first();

        if (!$catalog) {
            $this->command->warn('No default catalog found. Skipping category seeding.');
            return;
        }

        $toys = Category::create([
            'catalog_id' => $catalog->id,
            'name' => 'Toys',
            'slug' => 'toys',
            'description' => 'All kinds of toys for kids',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'catalog_id' => $catalog->id,
            'parent_id' => $toys->id,
            'name' => 'Puzzles',
            'slug' => 'puzzles',
            'description' => 'Educational puzzles',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'catalog_id' => $catalog->id,
            'parent_id' => $toys->id,
            'name' => 'Building Blocks',
            'slug' => 'building-blocks',
            'description' => 'Construction and building toys',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $clothing = Category::create([
            'catalog_id' => $catalog->id,
            'name' => 'Clothing',
            'slug' => 'clothing',
            'description' => 'Kids clothing and accessories',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'catalog_id' => $catalog->id,
            'parent_id' => $clothing->id,
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'catalog_id' => $catalog->id,
            'parent_id' => $clothing->id,
            'name' => 'Dresses',
            'slug' => 'dresses',
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }
}
