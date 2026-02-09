<?php

namespace Database\Seeders;

use App\Models\CatalogType;
use Illuminate\Database\Seeder;

class CatalogTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'General',
                'slug' => 'general',
                'description' => 'General products without specific category attributes',
                'icon' => 'fa-box',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Grocery',
                'slug' => 'grocery',
                'description' => 'Grocery items and food products',
                'icon' => 'fa-shopping-basket',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Clothing & Apparel',
                'slug' => 'clothing',
                'description' => 'Clothing, shoes, and fashion accessories',
                'icon' => 'fa-tshirt',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Toys & Games',
                'slug' => 'toys',
                'description' => 'Toys and games for children',
                'icon' => 'fa-gamepad',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Puzzles & Brain Teasers',
                'slug' => 'puzzles',
                'description' => 'Puzzles, brain teasers, and logic games',
                'icon' => 'fa-puzzle-piece',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Food & Beverages',
                'slug' => 'food',
                'description' => 'Food items and drinks',
                'icon' => 'fa-utensils',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and gadgets',
                'icon' => 'fa-microchip',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home',
                'description' => 'Home decor and garden supplies',
                'icon' => 'fa-home',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Beauty & Personal Care',
                'slug' => 'beauty',
                'description' => 'Beauty products and personal care items',
                'icon' => 'fa-spa',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports',
                'description' => 'Sports equipment and outdoor gear',
                'icon' => 'fa-futbol',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Books & Media',
                'slug' => 'books',
                'description' => 'Books, magazines, and media',
                'icon' => 'fa-book',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'Baby Products',
                'slug' => 'baby',
                'description' => 'Products for babies and toddlers',
                'icon' => 'fa-baby',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Health & Wellness',
                'slug' => 'health',
                'description' => 'Health and wellness products',
                'icon' => 'fa-heart',
                'is_active' => true,
                'sort_order' => 13,
            ],
        ];

        foreach ($types as $type) {
            CatalogType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
