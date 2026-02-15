<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Domains\Catalog\Models\Catalog;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        Catalog::firstOrCreate(
            ['slug' => 'retail'],
            [
                'name' => 'Retail Catalog',
                'type' => 'b2c',
                'description' => 'Main retail catalog for B2C customers',
                'is_active' => true,
                'is_default' => true,
            ]
        );

        Catalog::firstOrCreate(
            ['slug' => 'wholesale'],
            [
                'name' => 'Wholesale Catalog',
                'type' => 'wholesale',
                'description' => 'Wholesale catalog with bulk pricing',
                'pricing_rules' => json_encode([
                    'min_quantity' => 50,
                    'discount_percentage' => 15,
                ]),
                'is_active' => true,
                'is_default' => false,
            ]
        );

        Catalog::firstOrCreate(
            ['slug' => 'b2b'],
            [
                'name' => 'B2B Catalog',
                'type' => 'b2b',
                'description' => 'Business-to-business catalog',
                'pricing_rules' => json_encode([
                    'tiered_pricing' => [
                        ['min_qty' => 10, 'discount' => 5],
                        ['min_qty' => 50, 'discount' => 10],
                        ['min_qty' => 100, 'discount' => 15],
                    ],
                ]),
                'is_active' => true,
                'is_default' => false,
            ]
        );
    }
}
