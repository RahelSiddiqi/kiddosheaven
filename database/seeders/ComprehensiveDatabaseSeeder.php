<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PricingTemplate;
use App\Models\Setting;

class ComprehensiveDatabaseSeeder extends Seeder
{
    private $sampleImages = [];

    public function run(): void
    {
        $this->command->info('🚀 Starting comprehensive database seeding...');

        // Clear existing data
        $this->command->warn('Clearing existing data...');
        $this->clearData();

        // Prepare sample images
        $this->prepareSampleImages();

        // Seed data in order
        $this->seedSettings();
        $users = $this->seedUsers();
        $categories = $this->seedCategories();
        $this->seedPricingTemplates($categories);
        $brands = $this->seedBrands();
        $attributes = $this->seedProductAttributes();
        $this->assignAttributesToCategories($categories, $attributes);
        $products = $this->seedProducts($categories, $brands, $attributes);

        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('📊 Seeded: 21 users, ' . count($categories) . ' categories, 25 brands, 8 attributes, ' . count($products) . ' products');
    }

    private function clearData()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'order_items',
            'orders',
            'reviews',
            'inventory_movements',
            'purchase_batches',
            'product_variants',
            'product_attribute_values',
            'products',
            'product_attributes',
            'category_attributes',
            'brands',
            'categories',
            'category_pricing_template',
            'pricing_templates',
        ];

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Clear users except admin
        if (DB::getSchemaBuilder()->hasTable('users')) {
            DB::table('users')->where('email', '!=', 'admin@kiddosheaven.com')->delete();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function prepareSampleImages()
    {
        $publicPath = public_path('storage/products');

        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        $colors = [
            'red' => '#EF4444',
            'blue' => '#3B82F6',
            'green' => '#10B981',
            'yellow' => '#F59E0B',
            'purple' => '#8B5CF6',
            'pink' => '#EC4899',
            'orange' => '#F97316',
            'teal' => '#14B8A6',
        ];

        foreach ($colors as $name => $color) {
            $filename = "sample-{$name}.jpg";
            $path = $publicPath . '/' . $filename;

            if (!File::exists($path)) {
                if (function_exists('imagecreate')) {
                    $image = imagecreate(800, 800);
                    $rgb = sscanf($color, "#%02x%02x%02x");
                    imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
                    imagejpeg($image, $path, 90);
                    imagedestroy($image);
                }
            }

            $this->sampleImages[] = 'products/' . $filename;
        }

        $this->command->info('✓ Sample images prepared');
    }

    private function seedSettings()
    {
        $this->command->info('Seeding settings...');

        if (!Schema::hasTable('settings')) {
            $this->command->warn('⚠ Settings table not found, skipping...');
            return;
        }

        $hasTypeColumn = Schema::hasColumn('settings', 'type');

        $settingsData = [
            ['key' => 'site_name', 'value' => "Kiddo's Heaven"],
            ['key' => 'currency', 'value' => 'USD'],
            ['key' => 'low_stock_threshold', 'value' => '10'],
        ];

        foreach ($settingsData as $setting) {
            if ($hasTypeColumn) {
                $setting['type'] = is_numeric($setting['value']) ? 'number' : 'text';
            }
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✓ Settings seeded');
    }

    private function seedUsers()
    {
        $this->command->info('Seeding users...');

        $users = [];

        $users[] = User::firstOrCreate(
            ['email' => 'admin@kiddosheaven.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        for ($i = 1; $i <= 20; $i++) {
            $users[] = User::create([
                'name' => "Customer {$i}",
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]);
        }

        $this->command->info('✓ ' . count($users) . ' users seeded');
        return $users;
    }

    private function seedCategories()
    {
        $this->command->info('Seeding categories...');

        $icons = ['🧸', '👕', '📚', '👶', '🏃', '🎮'];

        $parentCategories = [
            ['name' => 'Toys & Games', 'icon' => '🧸', 'children' => [
                'Action Figures & Playsets',
                'Building Blocks & Construction',
                'Dolls & Accessories',
                'Educational Toys',
                'Remote Control Toys',
                'Board Games & Puzzles',
            ]],
            ['name' => 'Clothing', 'icon' => '👕', 'children' => [
                'Boys Clothing',
                'Girls Clothing',
                'Baby Clothing',
                'Shoes & Footwear',
            ]],
            ['name' => 'Books & Learning', 'icon' => '📚', 'children' => [
                'Picture Books',
                'Activity Books',
                'Learning Materials',
            ]],
            ['name' => 'Baby Products', 'icon' => '👶', 'children' => [
                'Feeding & Nursing',
                'Diapering',
                'Baby Safety',
            ]],
            ['name' => 'Outdoor & Sports', 'icon' => '🏃', 'children' => [
                'Bikes & Ride-Ons',
                'Sports Equipment',
                'Outdoor Play',
            ]],
        ];

        $allCategories = [];

        foreach ($parentCategories as $sortOrder => $parentData) {
            $parent = Category::create([
                'name' => $parentData['name'],
                'slug' => Str::slug($parentData['name']),
                'icon' => $parentData['icon'],
                'description' => 'Browse our ' . $parentData['name'] . ' collection',
                'show_on_home' => true,
                'is_active' => true,
                'sort_order' => $sortOrder + 1,
            ]);
            $allCategories[] = $parent;

            foreach ($parentData['children'] as $childSort => $childName) {
                $child = Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'parent_id' => $parent->id,
                    'description' => 'Browse our collection of ' . $childName,
                    'show_on_home' => (bool)rand(0, 1),
                    'is_active' => true,
                    'sort_order' => $childSort + 1,
                ]);
                $allCategories[] = $child;
            }
        }

        $this->command->info('✓ ' . count($allCategories) . ' categories seeded');
        return $allCategories;
    }

    private function seedPricingTemplates($categories)
    {
        $this->command->info('Seeding pricing templates...');

        if (!Schema::hasTable('pricing_templates')) {
            $this->command->warn('⚠ pricing_templates table not found, skipping...');
            return;
        }

        $this->call(PricingTemplateSeeder::class);

        $templates = PricingTemplate::all();
        if ($templates->isEmpty()) {
            $this->command->warn('⚠ No pricing templates created, skipping category linking');
            return;
        }

        $childCategories = collect($categories ?? [])->filter(fn($category) => $category->parent_id !== null)->values();
        if ($childCategories->isEmpty()) {
            $this->command->warn('⚠ No child categories available for linking pricing templates');
            return;
        }

        foreach ($templates as $template) {
            $assignable = min(3, $childCategories->count());
            $assigned = $childCategories->random(rand(1, $assignable))->pluck('id')->toArray();
            $template->categories()->sync($assigned);
        }

        $this->command->info('✓ ' . count($templates) . ' pricing templates linked to categories');
    }

    private function seedBrands()
    {
        $this->command->info('Seeding brands...');

        $brandsData = [
            'LEGO', 'Fisher-Price', 'Hasbro', 'Mattel', 'Melissa & Doug',
            'VTech', 'Little Tikes', 'Step2', 'Crayola', 'Play-Doh',
            'Hot Wheels', 'Barbie', 'Nerf', 'Disney', 'Marvel',
            'Paw Patrol', 'Peppa Pig', 'Baby Einstein', 'Graco', "Carter's",
            'Gerber', "The Children's Place", "OshKosh B'gosh", 'Nike Kids', 'Adidas Kids',
        ];

        $brands = [];
        foreach ($brandsData as $brandName) {
            $brands[] = Brand::create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
                'is_active' => true,
                'description' => 'Shop quality products from ' . $brandName,
            ]);
        }

        $this->command->info('✓ ' . count($brands) . ' brands seeded');
        return $brands;
    }

    private function seedProductAttributes()
    {
        $this->command->info('Seeding product attributes...');

        $attributes = [];

        // Color attribute
        $color = ProductAttribute::create([
            'name' => 'Color', 'slug' => 'color', 'type' => 'select',
            'is_required' => false, 'is_filterable' => true, 'sort_order' => 1,
        ]);
        foreach (['Red', 'Blue', 'Green', 'Yellow', 'Pink', 'Purple', 'Orange', 'Black', 'White', 'Multicolor'] as $i => $v) {
            ProductAttributeValue::create(['product_attribute_id' => $color->id, 'value' => $v, 'sort_order' => $i + 1]);
        }
        $attributes[] = $color;

        // Size attribute
        $size = ProductAttribute::create([
            'name' => 'Size', 'slug' => 'size', 'type' => 'select',
            'is_required' => false, 'is_filterable' => true, 'sort_order' => 2,
        ]);
        foreach (['Newborn', '0-3M', '3-6M', '6-9M', '9-12M', '12-18M', '18-24M', '2T', '3T', '4T', '5T', 'XS', 'S', 'M', 'L', 'XL'] as $i => $v) {
            ProductAttributeValue::create(['product_attribute_id' => $size->id, 'value' => $v, 'sort_order' => $i + 1]);
        }
        $attributes[] = $size;

        // Age Range
        $ageRange = ProductAttribute::create([
            'name' => 'Age Range', 'slug' => 'age-range', 'type' => 'select',
            'is_required' => false, 'is_filterable' => true, 'sort_order' => 3,
        ]);
        foreach (['0-6 months', '6-12 months', '1-2 years', '2-4 years', '4-7 years', '7-10 years', '10+ years'] as $i => $v) {
            ProductAttributeValue::create(['product_attribute_id' => $ageRange->id, 'value' => $v, 'sort_order' => $i + 1]);
        }
        $attributes[] = $ageRange;

        // Material
        $material = ProductAttribute::create([
            'name' => 'Material', 'slug' => 'material', 'type' => 'select',
            'is_required' => false, 'is_filterable' => true, 'sort_order' => 4,
        ]);
        foreach (['Plastic', 'Wood', 'Cotton', 'Polyester', 'Metal', 'Fabric', 'Silicone', 'Rubber'] as $i => $v) {
            ProductAttributeValue::create(['product_attribute_id' => $material->id, 'value' => $v, 'sort_order' => $i + 1]);
        }
        $attributes[] = $material;

        // Gender
        $gender = ProductAttribute::create([
            'name' => 'Gender', 'slug' => 'gender', 'type' => 'select',
            'is_required' => false, 'is_filterable' => true, 'sort_order' => 5,
        ]);
        foreach (['Boys', 'Girls', 'Unisex'] as $i => $v) {
            ProductAttributeValue::create(['product_attribute_id' => $gender->id, 'value' => $v, 'sort_order' => $i + 1]);
        }
        $attributes[] = $gender;

        // Number of Pieces
        $attributes[] = ProductAttribute::create([
            'name' => 'Number of Pieces', 'slug' => 'number-of-pieces', 'type' => 'number',
            'is_required' => false, 'is_filterable' => true, 'sort_order' => 6,
        ]);

        // Battery Required
        $attributes[] = ProductAttribute::create([
            'name' => 'Battery Required', 'slug' => 'battery-required', 'type' => 'boolean',
            'is_required' => false, 'is_filterable' => true, 'sort_order' => 7,
        ]);

        // Pattern
        $pattern = ProductAttribute::create([
            'name' => 'Pattern', 'slug' => 'pattern', 'type' => 'select',
            'is_required' => false, 'is_filterable' => true, 'sort_order' => 8,
        ]);
        foreach (['Solid', 'Striped', 'Polka Dot', 'Floral', 'Animal Print', 'Character'] as $i => $v) {
            ProductAttributeValue::create(['product_attribute_id' => $pattern->id, 'value' => $v, 'sort_order' => $i + 1]);
        }
        $attributes[] = $pattern;

        $this->command->info('✓ ' . count($attributes) . ' product attributes seeded');
        return $attributes;
    }

    private function assignAttributesToCategories($categories, $attributes)
    {
        $this->command->info('Assigning attributes to categories...');

        if (!Schema::hasTable('category_attributes')) {
            $this->command->warn('⚠ category_attributes table not found, skipping...');
            return;
        }

        $attributeIds = collect($attributes)->pluck('id')->toArray();

        // Only assign to child categories (which have products)
        $childCategories = collect($categories)->filter(fn($c) => $c->parent_id !== null);

        foreach ($childCategories as $category) {
            $randomCount = rand(3, min(6, count($attributeIds)));
            $randomAttributes = (array)array_rand(array_flip($attributeIds), $randomCount);
            $category->attributes()->sync($randomAttributes);
        }

        $this->command->info('✓ Attributes assigned to categories');
    }

    private function seedProducts($categories, $brands, $attributes)
    {
        $this->command->info('Seeding products...');

        $productTemplates = [
            ['name' => 'Building Block Set', 'description' => 'Creative building blocks for endless fun', 'base_price' => 29.99],
            ['name' => 'Action Figure', 'description' => 'Poseable action figure with accessories', 'base_price' => 19.99],
            ['name' => 'Doll House', 'description' => 'Beautiful dollhouse with furniture', 'base_price' => 89.99],
            ['name' => 'Remote Control Car', 'description' => 'Fast RC car with remote', 'base_price' => 39.99],
            ['name' => 'Puzzle Set', 'description' => 'Educational puzzle for kids', 'base_price' => 14.99],
            ['name' => 'Board Game', 'description' => 'Fun family board game', 'base_price' => 24.99],
            ['name' => 'Stuffed Animal', 'description' => 'Soft and cuddly plush toy', 'base_price' => 16.99],
            ['name' => 'Play Kitchen Set', 'description' => 'Interactive kitchen playset', 'base_price' => 79.99],
            ['name' => 'Train Set', 'description' => 'Complete train track set', 'base_price' => 54.99],
            ['name' => 'Art Supplies Kit', 'description' => 'Complete art set for young artists', 'base_price' => 22.99],
            ['name' => 'T-Shirt', 'description' => 'Comfortable cotton t-shirt', 'base_price' => 12.99],
            ['name' => 'Jeans', 'description' => 'Durable denim jeans', 'base_price' => 24.99],
            ['name' => 'Dress', 'description' => 'Pretty dress for special occasions', 'base_price' => 29.99],
            ['name' => 'Pajama Set', 'description' => 'Cozy pajamas for bedtime', 'base_price' => 18.99],
            ['name' => 'Jacket', 'description' => 'Warm jacket for cold days', 'base_price' => 44.99],
            ['name' => 'Sneakers', 'description' => 'Comfortable athletic shoes', 'base_price' => 39.99],
            ['name' => 'Shorts', 'description' => 'Comfortable summer shorts', 'base_price' => 15.99],
            ['name' => 'Hoodie', 'description' => 'Soft hooded sweatshirt', 'base_price' => 28.99],
            ['name' => 'Picture Book', 'description' => 'Colorful illustrated storybook', 'base_price' => 9.99],
            ['name' => 'Activity Book', 'description' => 'Fun activities and puzzles', 'base_price' => 7.99],
            ['name' => 'Coloring Book', 'description' => 'Creative coloring book', 'base_price' => 5.99],
            ['name' => 'Baby Bottle', 'description' => 'Anti-colic baby bottle', 'base_price' => 11.99],
            ['name' => 'Diaper Bag', 'description' => 'Spacious diaper bag', 'base_price' => 49.99],
            ['name' => 'Baby Monitor', 'description' => 'Video baby monitor', 'base_price' => 129.99],
            ['name' => 'Pacifier Set', 'description' => 'BPA-free pacifiers', 'base_price' => 8.99],
            ['name' => 'Baby Blanket', 'description' => 'Soft and warm blanket', 'base_price' => 19.99],
            ['name' => 'Bicycle', 'description' => 'Kids bicycle with training wheels', 'base_price' => 149.99],
            ['name' => 'Scooter', 'description' => 'Fun kick scooter', 'base_price' => 59.99],
            ['name' => 'Soccer Ball', 'description' => 'Durable soccer ball', 'base_price' => 17.99],
            ['name' => 'Basketball', 'description' => 'Youth size basketball', 'base_price' => 19.99],
            ['name' => 'Jump Rope', 'description' => 'Adjustable jump rope', 'base_price' => 6.99],
        ];

        // Filter to child categories only (products go in leaf categories)
        $childCategories = collect($categories)->filter(fn($c) => $c->parent_id !== null)->values();

        $products = [];
        $productCount = 0;

        foreach ($productTemplates as $template) {
            $variationsCount = rand(3, 5);

            for ($v = 0; $v < $variationsCount; $v++) {
                $brand = $brands[array_rand($brands)];
                $category = $childCategories->random();

                $name = $brand->name . ' ' . $template['name'] . ' ' . chr(65 + $v);
                $price = $template['base_price'] * (1 + (rand(-20, 30) / 100));
                $costPrice = round($price * (rand(40, 70) / 100), 2);
                $stock = rand(0, 200);

                $product = Product::create([
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . uniqid(),
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'description' => $template['description'] . ' - ' . $brand->name . ' quality product.',
                    'short_description' => $template['description'],
                    'price' => round($price, 2),
                    'cost_price' => $costPrice,
                    'discount_price' => rand(0, 100) > 70 ? round($price * 0.85, 2) : null,
                    'stock_quantity' => $stock,
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'is_active' => rand(0, 100) > 10,
                    'is_featured' => rand(0, 100) > 80,
                    'weight' => rand(100, 5000) / 100,
                    'length' => rand(10, 100),
                    'width' => rand(10, 100),
                    'height' => rand(5, 50),
                    'meta_title' => $name,
                    'meta_description' => $template['description'],
                ]);

                $products[] = $product;
                $productCount++;

                if ($productCount >= 120) break 2;
            }
        }

        $this->command->info('✓ ' . count($products) . ' products seeded');
        return collect($products);
    }
}
