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
use Illuminate\Support\Str;

class TeddyBearProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Clearing database tables...');

        // Clear tables in correct order (respecting foreign keys)
        \Illuminate\Support\Facades\DB::table('variant_attributes')->delete();
        ProductVariant::whereNotNull('id')->delete();
        Product::whereNotNull('id')->delete();
        \Illuminate\Support\Facades\DB::table('product_attribute_values')->delete();
        \Illuminate\Support\Facades\DB::table('product_attributes')->delete();
        Category::whereNotNull('id')->delete();
        Brand::whereNotNull('id')->delete();

        $this->command->info('Creating brands...');

        // Create Brands
        $brands = [
            [
                'name' => 'SnugglePaws',
                'slug' => 'snugglepaws',
                'description' => 'Premium quality soft toys for children',
                'is_active' => true,
            ],
            [
                'name' => 'CuddleJoy',
                'slug' => 'cuddlejoy',
                'description' => 'Eco-friendly and safe plush toys',
                'is_active' => true,
            ],
            [
                'name' => 'TinyHugs',
                'slug' => 'tinyhugs',
                'description' => 'Affordable cuddly companions for kids',
                'is_active' => true,
            ],
        ];

        $brandIds = [];
        foreach ($brands as $brandData) {
            $brand = Brand::create($brandData);
            $brandIds[$brand->slug] = $brand->id;
        }

        $this->command->info('Creating categories...');

        // Create Categories
        $toysCategory = Category::create([
            'name' => 'Toys',
            'slug' => 'toys',
            'description' => 'Fun and educational toys for kids',
            'icon' => '🧸',
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 1,
        ]);

        $teddyCategory = Category::create([
            'name' => 'Teddy Bears',
            'slug' => 'teddy-bears',
            'description' => 'Soft and cuddly teddy bears for children',
            'icon' => '🧸',
            'parent_id' => $toysCategory->id,
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 1,
        ]);

        $plushCategory = Category::create([
            'name' => 'Plush Toys',
            'slug' => 'plush-toys',
            'description' => 'Soft and huggable plush toys',
            'icon' => '🧸',
            'parent_id' => $toysCategory->id,
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 2,
        ]);

        $this->command->info('Creating size attribute...');

        // Create Size Attribute
        $sizeAttribute = ProductAttribute::create([
            'name' => 'Size',
            'slug' => 'size',
            'type' => 'select',
            'is_required' => true,
            'is_filterable' => true,
            'use_for_variants' => true,
            'description' => 'Size of the teddy bear',
            'sort_order' => 1,
        ]);

        // Create Size Values
        $sizeValues = [
            ['value' => 'Small', 'sort_order' => 1],
            ['value' => 'Medium', 'sort_order' => 2],
            ['value' => 'Large', 'sort_order' => 3],
            ['value' => 'Jumbo', 'sort_order' => 4],
        ];

        $sizeValueIds = [];
        foreach ($sizeValues as $size) {
            $sizeValue = ProductAttributeValue::create([
                'product_attribute_id' => $sizeAttribute->id,
                'value' => $size['value'],
                'sort_order' => $size['sort_order'],
            ]);
            $sizeValueIds[$size['value']] = $sizeValue->id;
        }

        $this->command->info('Reading product images...');

        // Get all images from storage
        $imagePath = storage_path('app/public/products');
        $images = glob($imagePath.'/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

        if (empty($images)) {
            $this->command->error('No images found in '.$imagePath);

            return;
        }

        $this->command->info('Found '.count($images).' images');

        // Product data based on teddy bears
        $productData = [
            [
                'name' => 'Classic Brown Teddy Bear',
                'description' => 'A timeless classic! Our Classic Brown Teddy Bear is the perfect companion for your little one. Made with ultra-soft premium polyester fibers and weighted for a realistic huggable feel. Features embroidered eyes and nose for safety.',
                'short_description' => 'Ultra-soft classic teddy bear with weighted bottom for realistic feel',
                'features' => ['100% premium polyester fill', 'Weighted bottom for realistic feel', 'Embroidered safety eyes and nose', 'Machine washable', 'ASTM F963 certified safe'],
                'care_instructions' => 'Surface wash recommended. Spot clean for best results. Air dry.',
                'safety_warning' => 'Not suitable for children under 3 years due to small parts.',
                'price' => 899,
                'discount_price' => 400,
                'cost_price' => 350,
                'sku' => 'TB-CLASSIC-BR',
                'weight' => 450,
                'length' => 25,
                'width' => 18,
                'height' => 35,
                'tags' => ['classic', 'brown', 'soft', 'huggable'],
                'images' => [
                    'products/FB_IMG_1769773749249.jpg',
                    'products/FB_IMG_1769773751447.jpg',
                ],
            ],
            [
                'name' => 'Pink Ribbon Teddy Bear',
                'description' => 'Adorable pink teddy bear with a cute ribbon bow! Perfect as a gift for birthdays, anniversaries, or just because. Super soft and cuddly with a lovely pink satin ribbon.',
                'short_description' => 'Adorable pink teddy with satin ribbon bow - perfect gift',
                'features' => ['Soft premium plush', 'Satin ribbon bow', 'Perfect gift item', 'Weighted bottom', 'Safe for all ages'],
                'care_instructions' => 'Gently spot clean. Do not bleach.',
                'safety_warning' => 'Adult supervision recommended for children under 3.',
                'price' => 749,
                'discount_price' => 250,
                'cost_price' => 280,
                'sku' => 'TB-PINK-RIBBON',
                'weight' => 380,
                'length' => 22,
                'width' => 16,
                'height' => 30,
                'tags' => ['pink', 'gift', 'ribbon', 'cute'],
                'images' => [
                    'products/IMG_20260129_222846.jpg',
                    'products/IMG_20260129_222911.jpg',
                ],
            ],
            [
                'name' => 'Cream White Teddy Bear',
                'description' => 'Elegant cream-colored teddy bear made with luxury soft fur. This sophisticated bear adds a touch of class to any nursery or bedroom decor.',
                'short_description' => 'Luxury cream teddy with extra soft fur',
                'features' => ['Luxury soft fur', 'Elegant cream color', 'Weighted bottom', 'Premium construction', 'Gift-ready packaging'],
                'care_instructions' => 'Professional dry clean recommended.',
                'safety_warning' => 'Keep away from fire.',
                'price' => 1199,
                'discount_price' => 400,
                'cost_price' => 450,
                'sku' => 'TB-CREAM-WHITE',
                'weight' => 520,
                'length' => 28,
                'width' => 20,
                'height' => 38,
                'tags' => ['cream', 'luxury', 'white', 'elegant'],
                'images' => [
                    'products/IMG_20260129_222942.jpg',
                    'products/IMG_20260129_223002.jpg',
                ],
            ],
            [
                'name' => 'Baby Blue Teddy Bear',
                'description' => 'Sweet baby blue teddy bear perfect for newborn gifts and baby showers. Extra gentle materials suitable for sensitive skin.',
                'short_description' => 'Gentle baby blue teddy - perfect newborn gift',
                'features' => ['Hypoallergenic materials', 'Baby-safe dyes', 'Extra soft texture', 'Cute bow tie accent', 'Ideal for baby showers'],
                'care_instructions' => 'Machine wash cold on gentle cycle. Air dry.',
                'safety_warning' => 'Remove ribbon before giving to infants.',
                'price' => 849,
                'discount_price' => 300,
                'cost_price' => 320,
                'sku' => 'TB-BABY-BLUE',
                'weight' => 350,
                'length' => 20,
                'width' => 15,
                'height' => 28,
                'tags' => ['blue', 'baby', 'gift', 'newborn'],
                'images' => [
                    'products/IMG_20260129_223057.jpg',
                    'products/IMG_20260129_223118.jpg',
                ],
            ],
            [
                'name' => 'Rainbow Teddy Bear',
                'description' => 'Colorful rainbow teddy bear with multi-colored fur! Each strand is carefully dyed for vibrant, long-lasting colors.',
                'short_description' => 'Vibrant rainbow colors - bring color to any room',
                'features' => ['Multi-colored fur', 'Vibrant long-lasting colors', 'Colorful fun design', 'Same-day happiness', 'Collector item'],
                'care_instructions' => 'Spot clean only. Avoid direct sunlight.',
                'safety_warning' => 'Colors may bleed on first wash.',
                'price' => 999,
                'discount_price' => 400,
                'cost_price' => 380,
                'sku' => 'TB-RAINBOW',
                'weight' => 420,
                'length' => 24,
                'width' => 17,
                'height' => 32,
                'tags' => ['rainbow', 'colorful', 'fun', 'vibrant'],
                'images' => [
                    'products/IMG_20260129_223131.jpg',
                    'products/IMG_20260129_223206.jpg',
                ],
            ],
            [
                'name' => 'Chocolate Brown Teddy Bear',
                'description' => 'Rich chocolate brown teddy bear with extra plush fur. Deep, warm color that looks beautiful in any setting.',
                'short_description' => 'Rich chocolate brown with ultra-plush fur',
                'features' => ['Deep chocolate color', 'Ultra-plush fur', 'Weighted bottom', 'Premium quality', 'Durable construction'],
                'care_instructions' => 'Surface wash with mild soap. Air dry.',
                'safety_warning' => 'Keep away from heat sources.',
                'price' => 949,
                'discount_price' => 350,
                'cost_price' => 360,
                'sku' => 'TB-CHOCOLATE',
                'weight' => 480,
                'length' => 26,
                'width' => 19,
                'height' => 36,
                'tags' => ['chocolate', 'brown', 'plush', 'premium'],
                'images' => [
                    'products/IMG_20260129_223227.jpg',
                    'products/IMG_20260129_223240.jpg',
                ],
            ],
            [
                'name' => 'White Snow Bear',
                'description' => 'Pure white snow bear inspired by Arctic beauty. Luxuriously soft with a subtle shimmer to the fur.',
                'short_description' => 'Pure white bear with shimmering soft fur',
                'features' => ['Pure white color', 'Shimmer effect fur', 'Luxury finish', 'Weighted bottom', 'Gift box included'],
                'care_instructions' => 'Professional clean only.',
                'safety_warning' => 'May show dirt easily - handle with care.',
                'price' => 1349,
                'discount_price' => 450,
                'cost_price' => 520,
                'sku' => 'TB-WHITE-SNOW',
                'weight' => 550,
                'length' => 29,
                'width' => 21,
                'height' => 40,
                'tags' => ['white', 'snow', 'luxury', 'premium'],
                'images' => [
                    'products/IMG_20260129_223305.jpg',
                    'products/IMG_20260129_223333.jpg',
                ],
            ],
            [
                'name' => 'Grey Silly Bear',
                'description' => 'Playful grey teddy with an expressively silly face! Different expressions that bring joy and laughter.',
                'short_description' => 'Grey teddy with silly expressive face',
                'features' => ['Expressive face', 'Playful design', 'Grey soft fur', 'Fun personality', 'Great conversation starter'],
                'care_instructions' => 'Spot clean recommended.',
                'safety_warning' => 'Not intended for rough play.',
                'price' => 799,
                'discount_price' => 300,
                'cost_price' => 290,
                'sku' => 'TB-GREY-SILLY',
                'weight' => 340,
                'length' => 21,
                'width' => 15,
                'height' => 28,
                'tags' => ['grey', 'silly', 'fun', 'playful'],
                'images' => [
                    'products/IMG_20260129_223344.jpg',
                    'products/IMG_20260129_223412.jpg',
                ],
            ],
            [
                'name' => 'Caramel Teddy Bear',
                'description' => 'Warm caramel colored bear with honey-toned fur. Like a sweet treat, this bear is irresistible to hug!',
                'short_description' => 'Warm caramel fur - sweet and huggable',
                'features' => ['Caramel honey color', 'Super soft fur', 'Warm inviting design', 'Cozy companion', 'All-ages friendly'],
                'care_instructions' => 'Machine wash cold, tumble dry low.',
                'safety_warning' => 'Supervise children during play.',
                'price' => 899,
                'discount_price' => 300,
                'cost_price' => 340,
                'sku' => 'TB-CARAMEL',
                'weight' => 410,
                'length' => 24,
                'width' => 17,
                'height' => 33,
                'tags' => ['caramel', 'warm', 'cozy', 'honey'],
                'images' => [
                    'products/IMG_20260129_223436.jpg',
                    'products/IMG_20260129_223605.jpg',
                ],
            ],
            [
                'name' => 'Beige Golden Bear',
                'description' => 'Golden beige teddy bear with a warm, luxurious appearance. Premium quality that feels as good as it looks.',
                'short_description' => 'Golden beige luxury teddy bear',
                'features' => ['Golden beige color', 'Luxury appearance', 'Premium quality', 'Weighted bottom', 'Elegant gift'],
                'care_instructions' => 'Professional recommended for best care.',
                'safety_warning' => 'Handle with clean hands.',
                'price' => 1099,
                'discount_price' => 400,
                'cost_price' => 420,
                'sku' => 'TB-GOLDEN-BEIGE',
                'weight' => 490,
                'length' => 27,
                'width' => 19,
                'height' => 37,
                'tags' => ['beige', 'golden', 'luxury', 'elegant'],
                'images' => [
                    'products/IMG_20260129_223638.jpg',
                    'products/IMG_20260129_223723.jpg',
                ],
            ],
        ];

        $this->command->info('Creating products with variants...');

        $brandKey = array_rand($brandIds);

        foreach ($productData as $index => $data) {
            // Use images from the array, fallback to remaining images
            $imageIndex = $index % count($images);
            $imageFiles = [];

            // For this product, we'll use 2 images
            if ($index < count($images)) {
                $imageFiles[] = basename($images[$imageIndex]);
                if ($index + 1 < count($images)) {
                    $imageFiles[] = basename($images[$index + 1]);
                }
            }

            $product = Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'category_id' => $teddyCategory->id,
                'brand_id' => $brandIds[array_rand($brandIds)],
                'product_type' => 'variable',
                'description' => $data['description'],
                'short_description' => $data['short_description'],
                'features' => json_encode($data['features']),
                'care_instructions' => $data['care_instructions'],
                'safety_warning' => $data['safety_warning'],
                'price' => $data['price'],
                'discount_price' => $data['discount_price'],
                'cost_price' => $data['cost_price'],
                'sku' => $data['sku'],
                'stock_quantity' => rand(20, 100),
                'weight' => $data['weight'],
                'length' => $data['length'],
                'width' => $data['width'],
                'height' => $data['height'],
                'tags' => $data['tags'],
                'primary_image' => 'products/'.($imageFiles[0] ?? null),
                'images' => array_map(fn ($img) => 'products/'.$img, $imageFiles),
                'is_featured' => $index < 5,
                'is_active' => true,
                'created_at' => now()->subDays(rand(1, 7)),
            ]);

            $this->command->info("Created product: {$product->name}");

            // Create variants for each size
            $sizes = [
                'Small' => ['price_modifier' => -200, 'weight_modifier' => -150, 'stock' => rand(10, 25)],
                'Medium' => ['price_modifier' => 0, 'weight_modifier' => 0, 'stock' => rand(15, 35)],
                'Large' => ['price_modifier' => 300, 'weight_modifier' => 200, 'stock' => rand(10, 20)],
                'Jumbo' => ['price_modifier' => 600, 'weight_modifier' => 400, 'stock' => rand(5, 15)],
            ];

            $isFirst = true;
            foreach ($sizes as $sizeName => $sizeData) {
                $variantPrice = $data['price'] + $sizeData['price_modifier'];
                $variantWeight = $data['weight'] + $sizeData['weight_modifier'];

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $data['sku'].'-'.strtoupper(substr($sizeName, 0, 2)),
                    'name' => $sizeName,
                    'price' => $variantPrice,
                    'cost_price' => round($variantPrice * 0.4, 2),
                    'compare_at_price' => $data['discount_price'] + $sizeData['price_modifier'],
                    'stock_quantity' => $sizeData['stock'],
                    'weight' => $variantWeight,
                    'length' => round($data['length'] * (1 + $sizeData['weight_modifier'] / 1000), 1),
                    'width' => round($data['width'] * (1 + $sizeData['weight_modifier'] / 1000), 1),
                    'height' => round($data['height'] * (1 + $sizeData['weight_modifier'] / 1000), 1),
                    'is_default' => $isFirst,
                    'is_active' => true,
                    'low_stock_alert' => 5,
                ]);

                // Link variant to size attribute
                VariantAttribute::create([
                    'product_variant_id' => $variant->id,
                    'product_attribute_id' => $sizeAttribute->id,
                    'product_attribute_value_id' => $sizeValueIds[$sizeName],
                ]);

                $isFirst = false;
                $this->command->info("  - Added {$sizeName} variant: ৳{$variantPrice} ({$sizeData['stock']} in stock)");
            }
        }

        // Create some additional plush toys category products
        $this->command->info('Creating plush toy products...');

        $plushProducts = [
            [
                'name' => 'Fluffy Bunny Rabbit',
                'description' => 'Adorable fluffy bunny rabbit with long, soft ears. Perfect for bunny lovers!',
                'short_description' => 'Cute bunny with fluffy soft ears',
                'features' => ['Long soft ears', 'Fluffy tail', 'Safe embroidered face', 'Machine washable'],
                'care_instructions' => 'Machine wash cold, air dry.',
                'price' => 649,
                'discount_price' => 250,
                'cost_price' => 240,
                'sku' => 'PLUSH-BUNNY',
                'tags' => ['bunny', 'rabbit', 'fluffy', 'cute'],
            ],
            [
                'name' => 'Cute Puppy Dog',
                'description' => 'Adorable puppy plush toy with floppy ears and a sweet expression. Perfect playtime companion!',
                'short_description' => 'Sweet puppy with floppy ears',
                'features' => ['Floppy ears', 'Sweet expression', 'Soft huggable body', 'Safe materials'],
                'care_instructions' => 'Spot clean recommended.',
                'price' => 599,
                'discount_price' => 200,
                'cost_price' => 220,
                'sku' => 'PLUSH-PUPPY',
                'tags' => ['puppy', 'dog', 'cute', 'floppy'],
            ],
        ];

        foreach ($plushProducts as $plushData) {
            $product = Product::create([
                'name' => $plushData['name'],
                'slug' => Str::slug($plushData['name']),
                'category_id' => $plushCategory->id,
                'brand_id' => $brandIds[array_rand($brandIds)],
                'product_type' => 'simple',
                'description' => $plushData['description'],
                'short_description' => $plushData['short_description'],
                'features' => json_encode($plushData['features']),
                'care_instructions' => $plushData['care_instructions'],
                'price' => $plushData['price'],
                'discount_price' => $plushData['discount_price'],
                'cost_price' => $plushData['cost_price'],
                'sku' => $plushData['sku'],
                'stock_quantity' => rand(25, 50),
                'tags' => $plushData['tags'],
                'is_featured' => true,
                'is_active' => true,
            ]);

            $this->command->info("Created plush product: {$product->name}");
        }

        $this->command->info('✅ Seeding completed!');
        $this->command->info('- Created '.count($brands).' brands');
        $this->command->info('- Created categories: Toys, Teddy Bears, Plush Toys');
        $this->command->info('- Created '.count($productData).' teddy bear products with variants');
        $this->command->info('- Created '.count($plushProducts).' plush toy products');
    }
}
