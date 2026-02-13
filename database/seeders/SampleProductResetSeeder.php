<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleProductResetSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Ensure core attributes exist
        $colorAttr = ProductAttribute::firstOrCreate(
            ['slug' => 'color'],
            ['name' => 'Color', 'type' => 'select', 'use_for_variants' => true]
        );
        $sizeAttr = ProductAttribute::firstOrCreate(
            ['slug' => 'size'],
            ['name' => 'Size', 'type' => 'select', 'use_for_variants' => true]
        );

        $colors = collect(['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White', 'Pink'])
            ->map(fn($c) => ProductAttributeValue::firstOrCreate([
                'product_attribute_id' => $colorAttr->id,
                'value' => $c,
            ]));

        $sizes = collect(['XS', 'S', 'M', 'L', 'XL'])
            ->map(fn($s) => ProductAttributeValue::firstOrCreate([
                'product_attribute_id' => $sizeAttr->id,
                'value' => $s,
            ]));

        // Baseline taxonomies
        $categoryNames = ['Building Blocks & Construction', 'Plush & Soft Toys', 'Educational', 'Outdoor', 'Arts & Crafts', 'Electronics', 'Baby & Toddler'];
        $brandNames = ['Peppa Pig', 'Lego', 'Fisher Price', 'Hot Wheels', 'Barbie', 'Hasbro', 'Mattel'];

        $categories = collect($categoryNames)->map(fn($name) => Category::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name]
        ));

        $brands = collect($brandNames)->map(fn($name) => Brand::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name]
        ));

        // Clear existing product data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        VariantAttribute::truncate();
        ProductVariant::truncate();
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $productsToCreate = 100;
        $variableRatio = 0.4; // 40% variable, 60% simple

        for ($i = 1; $i <= $productsToCreate; $i++) {
            $isVariable = $faker->boolean($variableRatio * 100);
            $name = $faker->unique()->words(3, true);
            $basePrice = $faker->numberBetween(500, 5500);
            $cost = (int) round($basePrice * $faker->randomFloat(2, 0.4, 0.8));

            $product = Product::create([
                'name' => Str::title($name),
                'slug' => Str::slug($name . '-' . $i),
                'category_id' => $categories->random()->id,
                'brand_id' => $brands->random()->id,
                'product_type' => $isVariable ? 'variable' : 'simple',
                'sku' => 'SKU-' . strtoupper(Str::random(6)),
                'barcode' => $faker->numerify('#############'),
                'price' => $basePrice,
                'cost_price' => $cost,
                'profit_margin' => $basePrice > 0 ? (($basePrice - $cost) / $basePrice) * 100 : 0,
                'stock_quantity' => $isVariable ? 0 : $faker->numberBetween(5, 120),
                'low_stock_alert' => $faker->randomElement([5, 10, 15]),
                'stock_status' => 'in_stock',
                'weight' => $faker->randomFloat(2, 0.1, 5),
                'length' => $faker->randomFloat(1, 5, 40),
                'width' => $faker->randomFloat(1, 5, 40),
                'height' => $faker->randomFloat(1, 5, 40),
                'short_description' => $faker->sentence(8),
                'description' => '<p>' . $faker->paragraph(3) . '</p>',
                'features' => "• " . implode("\n• ", $faker->words(4)),
                'tags' => $faker->randomElements(['toys', 'educational', 'outdoor', 'plush', 'new', 'bestseller'], $faker->numberBetween(1, 3)),
                'images' => ['products/sample-' . (($i % 10) + 1) . '.jpg'],
                'primary_image' => 'products/sample-' . (($i % 10) + 1) . '.jpg',
                'is_featured' => $faker->boolean(20),
                'is_active' => $faker->boolean(90),
                'status' => 'active',
                'meta_title' => $faker->sentence(5),
                'meta_description' => $faker->sentence(12),
            ]);

            if ($isVariable) {
                $this->seedVariantsForProduct($product, $colors, $sizes, $faker);
            }
        }

        $this->command->info("✅ Reset products and seeded {$productsToCreate} sample products.");
    }

    private function seedVariantsForProduct(Product $product, $colors, $sizes, $faker): void
    {
        $colorChoices = $colors->random($faker->numberBetween(2, 3));
        $sizeChoices = $sizes->random($faker->numberBetween(2, 4));

        $isFirst = true;
        foreach ($colorChoices as $color) {
            foreach ($sizeChoices as $size) {
                $price = $product->price + $faker->numberBetween(-200, 400);
                $cost = max(50, $price - $faker->numberBetween(50, 200));

                $variant = $product->variants()->create([
                    'sku' => $product->sku . '-' . strtoupper(substr($color->value, 0, 3)) . '-' . strtoupper($size->value),
                    'price' => $price,
                    'cost_price' => $cost,
                    'compare_at_price' => $price + $faker->numberBetween(50, 300),
                    'stock_quantity' => $faker->numberBetween(5, 60),
                    'reserved_quantity' => 0,
                    'barcode' => $faker->numerify('#############'),
                    'weight' => $faker->randomFloat(2, 0.1, 5),
                    'is_default' => $isFirst,
                    'is_active' => $faker->boolean(95),
                ]);

                VariantAttribute::create([
                    'product_variant_id' => $variant->id,
                    'product_attribute_id' => $color->product_attribute_id,
                    'product_attribute_value_id' => $color->id,
                ]);

                VariantAttribute::create([
                    'product_variant_id' => $variant->id,
                    'product_attribute_id' => $size->product_attribute_id,
                    'product_attribute_value_id' => $size->id,
                ]);

                $isFirst = false;
            }
        }
    }
}
