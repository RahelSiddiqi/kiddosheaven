<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Str;

class ProductWithVariantsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding products with variants...');

        // Get necessary data
        $clothingCategory = Category::where('name', 'LIKE', '%Clothing%')->orWhere('name', 'LIKE', '%Fashion%')->first();
        $toyCategory = Category::where('name', 'LIKE', '%Toy%')->first();
        $foodCategory = Category::where('name', 'LIKE', '%Food%')->orWhere('name', 'LIKE', '%Nutrition%')->first();
        $brand = Brand::first();

        // Get attributes
        $colorAttr = ProductAttribute::where('name', 'Color')->first();
        $sizeAttr = ProductAttribute::where('name', 'Size')->first();
        $weightAttr = ProductAttribute::where('name', 'Weight')->first();

        // 1. Simple Product (Toy)
        $this->createSimpleProduct($toyCategory, $brand);

        // 2. Variable Product - T-Shirt (Color × Size)
        if ($clothingCategory && $colorAttr && $sizeAttr) {
            $this->createTShirtWithVariants($clothingCategory, $brand, $colorAttr, $sizeAttr);
        }

        // 3. Variable Product - Rice Pack (Weight)
        if ($foodCategory && $weightAttr) {
            $this->createRiceWithVariants($foodCategory, $brand, $weightAttr);
        }

        // 4. Variable Product - Complex (Color × Size × Weight)
        if ($clothingCategory && $colorAttr && $sizeAttr && $weightAttr) {
            $this->createComplexVariantProduct($clothingCategory, $brand, $colorAttr, $sizeAttr, $weightAttr);
        }

        // 5. Digital Product
        $this->createDigitalProduct($toyCategory, $brand);

        // 6. Featured Products
        $this->createFeaturedProducts($clothingCategory, $brand);

        $this->command->info('Products with variants seeded successfully!');
    }

    private function createSimpleProduct($category, $brand)
    {
        $this->command->info('Creating simple product...');

        $product = Product::create([
            'name' => 'Wooden Building Blocks Set',
            'slug' => 'wooden-building-blocks-set',
            'category_id' => $category?->id ?? 1,
            'brand_id' => $brand?->id,
            'product_type' => 'simple',
            'sku' => 'TOY-WBB-001',
            'barcode' => '8901234567890',
            'price' => 45000, // 450 BDT
            'cost_price' => 28000, // 280 BDT
            'profit_margin' => 37.78,
            'stock_quantity' => 50,
            'low_stock_alert' => 10,
            'stock_status' => 'in_stock',
            'weight' => 1.5,
            'short_description' => 'Classic wooden building blocks for creative play',
            'description' => '<p>High-quality wooden building blocks perfect for developing motor skills and creativity. Safe, non-toxic paint. Suitable for ages 3+.</p>',
            'features' => "• 50 colorful blocks\n• Non-toxic paint\n• Smooth edges\n• Storage box included",
            'images' => ['products/blocks-1.jpg', 'products/blocks-2.jpg'],
            'primary_image' => 'products/blocks-1.jpg',
            'tags' => ['toys', 'educational', 'wooden'],
            'is_featured' => false,
            'is_active' => true,
            'meta_title' => 'Wooden Building Blocks Set - Educational Toy',
            'meta_description' => 'High-quality wooden building blocks for creative play and learning.',
            'safety_warning' => 'Not suitable for children under 3 years - choking hazard',
        ]);

        $this->command->info("✓ Created simple product: {$product->name}");
    }

    private function createTShirtWithVariants($category, $brand, $colorAttr, $sizeAttr)
    {
        $this->command->info('Creating T-Shirt with variants...');

        $product = Product::create([
            'name' => 'Premium Cotton T-Shirt',
            'slug' => 'premium-cotton-t-shirt',
            'category_id' => $category->id,
            'brand_id' => $brand?->id,
            'product_type' => 'variable',
            'sku' => 'CLOTH-TSH-001',
            'price' => 50000, // Base price 500 BDT
            'cost_price' => 28000, // 280 BDT
            'profit_margin' => 44.0,
            'stock_quantity' => 0, // Stock tracked per variant
            'low_stock_alert' => 5,
            'stock_status' => 'in_stock',
            'weight' => 0.2,
            'short_description' => '100% premium cotton t-shirt, soft and comfortable',
            'description' => '<p>Made from 100% premium cotton fabric. Breathable, soft, and perfect for everyday wear. Available in multiple colors and sizes.</p>',
            'features' => "• 100% premium cotton\n• Pre-shrunk fabric\n• Reinforced stitching\n• Machine washable",
            'images' => ['products/tshirt-1.jpg', 'products/tshirt-2.jpg', 'products/tshirt-3.jpg'],
            'primary_image' => 'products/tshirt-1.jpg',
            'tags' => ['clothing', 'casual', 'cotton'],
            'is_featured' => true,
            'is_active' => true,
            'meta_title' => 'Premium Cotton T-Shirt - Multiple Colors & Sizes',
            'meta_description' => '100% premium cotton t-shirt in various colors and sizes. Soft, comfortable, and durable.',
            'care_instructions' => 'Machine wash cold, tumble dry low, do not bleach',
        ]);

        // Get color and size values
        $colors = [
            ['name' => 'Red', 'value' => 'red', 'code' => 'RED'],
            ['name' => 'Blue', 'value' => 'blue', 'code' => 'BLU'],
            ['name' => 'Green', 'value' => 'green', 'code' => 'GRN'],
        ];

        $sizes = [
            ['name' => 'Small', 'value' => 'S', 'code' => 'SMA'],
            ['name' => 'Medium', 'value' => 'M', 'code' => 'MED'],
            ['name' => 'Large', 'value' => 'L', 'code' => 'LAR'],
        ];

        $isFirst = true;
        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                $variant = $product->variants()->create([
                    'sku' => "CLOTH-TSH-{$color['code']}-{$size['code']}",
                    'price' => $size['value'] === 'L' ? 55000 : 50000, // Large costs more
                    'cost_price' => 28000,
                    'stock_quantity' => rand(5, 20),
                    'reserved_quantity' => 0,
                    'barcode' => '890' . rand(1000000, 9999999),
                    'weight' => 0.2,
                    'is_default' => $isFirst,
                    'is_active' => true,
                ]);

                // Link attributes (we'll need to create attribute value records)
                $this->linkVariantAttribute($variant, $colorAttr, $color['name']);
                $this->linkVariantAttribute($variant, $sizeAttr, $size['name']);

                $isFirst = false;
            }
        }

        $this->command->info("✓ Created variable product with 9 variants: {$product->name}");
    }

    private function createRiceWithVariants($category, $brand, $weightAttr)
    {
        $this->command->info('Creating Rice Pack with weight variants...');

        $product = Product::create([
            'name' => 'Premium Basmati Rice',
            'slug' => 'premium-basmati-rice',
            'category_id' => $category->id,
            'brand_id' => $brand?->id,
            'product_type' => 'variable',
            'sku' => 'FOOD-RICE-001',
            'price' => 10000, // Base price 100 BDT
            'cost_price' => 6000, // 60 BDT
            'profit_margin' => 40.0,
            'stock_quantity' => 0,
            'low_stock_alert' => 10,
            'stock_status' => 'in_stock',
            'short_description' => 'Premium quality basmati rice',
            'description' => '<p>Finest quality aged basmati rice. Long grain, aromatic, and fluffy when cooked. Perfect for biryani and everyday meals.</p>',
            'features' => "• 100% pure basmati\n• Aged for flavor\n• Long grain\n• Low GI",
            'images' => ['products/rice-1.jpg'],
            'primary_image' => 'products/rice-1.jpg',
            'tags' => ['food', 'rice', 'basmati'],
            'is_featured' => false,
            'is_active' => true,
            'meta_title' => 'Premium Basmati Rice - Multiple Pack Sizes',
            'ingredients' => '100% Basmati Rice',
        ]);

        $weights = [
            ['name' => '500g', 'value' => 0.5, 'code' => '500G', 'price' => 5000, 'cost' => 3000],
            ['name' => '1kg', 'value' => 1.0, 'code' => '1KG', 'price' => 9500, 'cost' => 5700],
            ['name' => '2kg', 'value' => 2.0, 'code' => '2KG', 'price' => 18000, 'cost' => 10800],
            ['name' => '5kg', 'value' => 5.0, 'code' => '5KG', 'price' => 42500, 'cost' => 25500],
        ];

        $isFirst = true;
        foreach ($weights as $weight) {
            $variant = $product->variants()->create([
                'sku' => "FOOD-RICE-{$weight['code']}",
                'price' => $weight['price'],
                'cost_price' => $weight['cost'],
                'stock_quantity' => rand(20, 100),
                'reserved_quantity' => 0,
                'barcode' => '890' . rand(1000000, 9999999),
                'weight' => $weight['value'],
                'is_default' => $isFirst,
                'is_active' => true,
            ]);

            $this->linkVariantAttribute($variant, $weightAttr, $weight['name']);
            $isFirst = false;
        }

        $this->command->info("✓ Created variable product with 4 weight variants: {$product->name}");
    }

    private function createComplexVariantProduct($category, $brand, $colorAttr, $sizeAttr, $weightAttr)
    {
        $this->command->info('Creating complex multi-attribute product...');

        $product = Product::create([
            'name' => 'Weighted Training Vest',
            'slug' => 'weighted-training-vest',
            'category_id' => $category->id,
            'brand_id' => $brand?->id,
            'product_type' => 'variable',
            'sku' => 'SPORT-VEST-001',
            'price' => 350000, // Base price 3500 BDT
            'cost_price' => 210000,
            'profit_margin' => 40.0,
            'stock_quantity' => 0,
            'low_stock_alert' => 3,
            'stock_status' => 'in_stock',
            'short_description' => 'Adjustable weighted vest for strength training',
            'description' => '<p>Professional weighted training vest with removable weights. Perfect for CrossFit, running, and bodyweight exercises.</p>',
            'features' => "• Adjustable weight\n• Comfortable fit\n• Reflective strips\n• Breathable mesh",
            'images' => ['products/vest-1.jpg', 'products/vest-2.jpg'],
            'primary_image' => 'products/vest-1.jpg',
            'tags' => ['sports', 'fitness', 'training'],
            'is_featured' => true,
            'is_active' => true,
            'meta_title' => 'Weighted Training Vest - Adjustable Fitness Equipment',
        ]);

        $colors = [
            ['name' => 'Black', 'code' => 'BLK'],
            ['name' => 'Gray', 'code' => 'GRY'],
        ];

        $sizes = [
            ['name' => 'M', 'code' => 'M'],
            ['name' => 'L', 'code' => 'L'],
            ['name' => 'XL', 'code' => 'XL'],
        ];

        $weights = [
            ['name' => '5kg', 'value' => 5, 'code' => '5KG', 'priceAdd' => 0],
            ['name' => '10kg', 'value' => 10, 'code' => '10K', 'priceAdd' => 50000],
            ['name' => '15kg', 'value' => 15, 'code' => '15K', 'priceAdd' => 100000],
        ];

        $isFirst = true;
        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                foreach ($weights as $weight) {
                    $sizeMultiplier = $size['code'] === 'XL' ? 20000 : ($size['code'] === 'L' ? 10000 : 0);

                    $variant = $product->variants()->create([
                        'sku' => "SPORT-VEST-{$color['code']}-{$size['code']}-{$weight['code']}",
                        'price' => 350000 + $weight['priceAdd'] + $sizeMultiplier,
                        'cost_price' => 210000 + ($weight['priceAdd'] * 0.6) + ($sizeMultiplier * 0.6),
                        'stock_quantity' => rand(2, 8),
                        'reserved_quantity' => 0,
                        'barcode' => '890' . rand(1000000, 9999999),
                        'weight' => $weight['value'],
                        'is_default' => $isFirst,
                        'is_active' => true,
                    ]);

                    $this->linkVariantAttribute($variant, $colorAttr, $color['name']);
                    $this->linkVariantAttribute($variant, $sizeAttr, $size['name']);
                    $this->linkVariantAttribute($variant, $weightAttr, $weight['name']);

                    $isFirst = false;
                }
            }
        }

        $this->command->info("✓ Created complex product with 18 variants (2×3×3): {$product->name}");
    }

    private function createDigitalProduct($category, $brand)
    {
        $this->command->info('Creating digital product...');

        $product = Product::create([
            'name' => 'Educational E-Book Bundle',
            'slug' => 'educational-ebook-bundle',
            'category_id' => $category?->id ?? 1,
            'brand_id' => $brand?->id,
            'product_type' => 'digital',
            'sku' => 'DIGI-EBOOK-001',
            'price' => 29900, // 299 BDT
            'cost_price' => 0, // Digital product
            'profit_margin' => 100.0,
            'stock_quantity' => 9999, // Unlimited
            'stock_status' => 'in_stock',
            'weight' => 0,
            'short_description' => 'Collection of 10 educational e-books for children',
            'description' => '<p>Downloadable bundle of 10 educational e-books. Instant download after purchase. PDF format compatible with all devices.</p>',
            'features' => "• 10 e-books\n• Instant download\n• PDF format\n• Lifetime access",
            'images' => ['products/ebook-1.jpg'],
            'primary_image' => 'products/ebook-1.jpg',
            'tags' => ['digital', 'books', 'educational'],
            'is_featured' => false,
            'is_active' => true,
            'meta_title' => 'Educational E-Book Bundle - Instant Download',
        ]);

        $this->command->info("✓ Created digital product: {$product->name}");
    }

    private function createFeaturedProducts($category, $brand)
    {
        $this->command->info('Creating featured products...');

        $products = [
            [
                'name' => 'Kids Winter Jacket',
                'slug' => 'kids-winter-jacket',
                'sku' => 'CLOTH-JKT-001',
                'price' => 125000,
                'cost_price' => 75000,
            ],
            [
                'name' => 'Baby Care Gift Set',
                'slug' => 'baby-care-gift-set',
                'sku' => 'BABY-GIFT-001',
                'price' => 89000,
                'cost_price' => 53400,
            ],
        ];

        foreach ($products as $data) {
            Product::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'category_id' => $category?->id ?? 1,
                'brand_id' => $brand?->id,
                'product_type' => 'simple',
                'sku' => $data['sku'],
                'barcode' => '890' . rand(1000000, 9999999),
                'price' => $data['price'],
                'cost_price' => $data['cost_price'],
                'profit_margin' => (($data['price'] - $data['cost_price']) / $data['price']) * 100,
                'stock_quantity' => rand(15, 40),
                'low_stock_alert' => 5,
                'stock_status' => 'in_stock',
                'weight' => 0.5,
                'short_description' => 'Premium quality product',
                'description' => '<p>High-quality product perfect for your needs.</p>',
                'images' => ['products/featured-1.jpg'],
                'primary_image' => 'products/featured-1.jpg',
                'tags' => ['featured', 'popular'],
                'is_featured' => true,
                'is_active' => true,
                'meta_title' => $data['name'],
            ]);
        }

        $this->command->info("✓ Created 2 featured products");
    }

    private function linkVariantAttribute($variant, $attribute, $valueName)
    {
        if (!$attribute) return;

        // Find or create attribute value
        $attributeValue = ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $attribute->id,
            'value' => $valueName,
        ], [
            'sort_order' => 0,
        ]);

        // Link to variant
        $variant->variantAttributes()->create([
            'product_attribute_id' => $attribute->id,
            'product_attribute_value_id' => $attributeValue->id,
        ]);
    }
}
