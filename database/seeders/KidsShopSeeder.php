<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class KidsShopSeeder extends Seeder
{
    private $users = [];
    private $categories = [];
    private $brands = [];
    private $attributes = [];
    private $products = [];

    public function run(): void
    {
        $this->command->info('🧸 Starting Kiddo\'s Heaven comprehensive seeding...');

        // Clear all data first
        $this->clearAllData();

        // Seed in order
        $this->seedUsers();
        $this->seedBrands();
        $this->seedCategories();
        $this->seedAttributes();
        $this->assignAttributesToCategories();
        $this->seedProducts();
        $this->seedOrders();
        $this->seedExpensesAndInvestments();

        $this->command->info('✅ Kiddo\'s Heaven shop seeded successfully!');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Users', count($this->users)],
                ['Categories', count($this->categories)],
                ['Brands', count($this->brands)],
                ['Attributes', count($this->attributes)],
                ['Products', count($this->products)],
            ]
        );
    }

    private function clearAllData()
    {
        $this->command->warn('🗑️  Clearing all existing data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'order_items', 'orders', 'reviews', 'inventory_movements', 'purchase_batches',
            'variant_attributes', 'product_variants', 'product_attribute_values',
            'category_attributes', 'products', 'product_attributes', 'brands', 'categories',
            'expenses', 'investments', 'partner_payments', 'partners',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->line("  ✓ Cleared: {$table}");
            }
        }

        // Clear users except those with specific emails
        if (Schema::hasTable('users')) {
            DB::table('users')->whereNotIn('email', ['admin@kiddosheaven.com', 'staff@kiddosheaven.com'])->delete();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('✓ Data cleared');
    }

    private function seedUsers()
    {
        $this->command->info('👤 Seeding users...');

        // Admin
        $this->users[] = User::firstOrCreate(
            ['email' => 'admin@kiddosheaven.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // Staff
        $this->users[] = User::firstOrCreate(
            ['email' => 'staff@kiddosheaven.com'],
            [
                'name' => 'Staff Member',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // Regular customers
        $customerNames = [
            'Sarah Ahmed', 'Mohammad Rahman', 'Fatima Khan', 'Ahmed Ali', 'Nadia Hossain',
            'Karim Hassan', 'Aisha Mahmud', 'Omar Siddiqui', 'Zainab Sheikh', 'Hassan Rahman',
            'Maryam Akhtar', 'Ibrahim Khan', 'Amina Chowdhury', 'Yusuf Ahmed', 'Khadija Begum',
            'Ali Hasan', 'Hafsa Malik', 'Bilal Rahman', 'Sadia Parveen', 'Tariq Hussain',
            'Layla Faisal', 'Hamza Iqbal', 'Ayesha Farooq', 'Usman Noor', 'Fatima Zahra',
            'Abdullah Sharif', 'Mariam Khalid', 'Imran Akram', 'Zara Baig', 'Salman Bashir',
        ];

        foreach ($customerNames as $i => $name) {
            $this->users[] = User::create([
                'name' => $name,
                'email' => 'customer' . ($i + 1) . '@email.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('✓ ' . count($this->users) . ' users seeded');
    }

    private function seedBrands()
    {
        $this->command->info('🏷️  Seeding brands...');

        $brandsData = [
            // Toy brands
            'LEGO', 'Fisher-Price', 'Hasbro', 'Mattel', 'Melissa & Doug', 'VTech', 'Little Tikes',
            'Step2', 'Crayola', 'Play-Doh', 'Hot Wheels', 'Barbie', 'Nerf', 'Playmobil',

            // Character brands
            'Disney', 'Marvel', 'Paw Patrol', 'Peppa Pig', 'Baby Shark', 'Frozen',

            // Clothing brands
            "Carter's", 'Gerber', "The Children's Place", "OshKosh B'gosh", 'Nike Kids',
            'Adidas Kids', 'H&M Kids', 'Zara Kids', 'Gap Kids', 'Gymboree',

            // Baby brands
            'Pampers', 'Huggies', 'Johnson\'s Baby', 'Baby Einstein', 'Graco', 'Chicco',

            // Food brands
            'Gerber', 'Heinz Baby', 'Earth\'s Best', 'Happy Baby', 'Plum Organics',

            // Book publishers
            'Scholastic', 'Penguin Random House', 'Usborne', 'DK Publishing',
        ];

        // Ensure uniqueness (e.g., Gerber appears in two groups)
        $brandsData = array_values(array_unique($brandsData));

        foreach ($brandsData as $name) {
            $this->brands[] = Brand::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'is_active' => true,
                    'description' => 'Shop quality products from ' . $name,
                ]
            );
        }

        $this->command->info('✓ ' . count($this->brands) . ' brands seeded');
    }

    private function seedCategories()
    {
        $this->command->info('📁 Seeding categories...');

        $structure = [
            ['name' => 'Toys & Games', 'icon' => '🧸', 'children' => [
                'Action Figures & Dolls',
                'Building Toys & Blocks',
                'Puzzles & Board Games',
                'Educational Toys',
                'Pretend Play & Dress-Up',
                'Remote Control & Vehicles',
                'Stuffed Animals & Plush',
                'Arts & Crafts',
            ]],
            ['name' => 'Clothing & Accessories', 'icon' => '👕', 'children' => [
                'Boys Clothing',
                'Girls Clothing',
                'Baby Clothing (0-24M)',
                'Toddler Clothing (2T-5T)',
                'Kids Shoes',
                'Accessories & Jewelry',
                'Sleepwear & Robes',
                'Outerwear & Jackets',
            ]],
            ['name' => 'Baby Essentials', 'icon' => '👶', 'children' => [
                'Feeding & Nursing',
                'Diapering & Potty',
                'Baby Care & Health',
                'Baby Safety',
                'Baby Gear & Furniture',
                'Baby Monitors & Electronics',
            ]],
            ['name' => 'Books & Learning', 'icon' => '📚', 'children' => [
                'Picture Books',
                'Early Readers',
                'Chapter Books',
                'Activity & Coloring Books',
                'Educational Workbooks',
                'Board Books',
            ]],
            ['name' => 'Kids Food & Nutrition', 'icon' => '🍎', 'children' => [
                'Baby Formula',
                'Baby Food & Snacks',
                'Toddler Snacks',
                'Kids Vitamins',
                'Organic Options',
            ]],
            ['name' => 'Outdoor & Sports', 'icon' => '⚽', 'children' => [
                'Bikes & Ride-Ons',
                'Sports Equipment',
                'Outdoor Play',
                'Beach & Pool Toys',
                'Trampolines & Playsets',
            ]],
            ['name' => 'Room & Decor', 'icon' => '🛏️', 'children' => [
                'Bedding',
                'Furniture',
                'Storage & Organization',
                'Wall Decor',
                'Night Lights',
            ]],
            ['name' => 'Party & Celebrations', 'icon' => '🎉', 'children' => [
                'Party Supplies',
                'Birthday Decorations',
                'Costumes',
                'Gift Sets',
            ]],
        ];

        foreach ($structure as $index => $parent) {
            $parentCat = Category::create([
                'name' => $parent['name'],
                'slug' => Str::slug($parent['name']),
                'icon' => $parent['icon'],
                'description' => 'Browse our ' . $parent['name'] . ' collection for kids',
                'show_on_home' => true,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
            $this->categories[] = $parentCat;

            foreach ($parent['children'] as $childIndex => $childName) {
                $childCat = Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'parent_id' => $parentCat->id,
                    'description' => 'Shop ' . $childName . ' for children',
                    'is_active' => true,
                    'sort_order' => $childIndex + 1,
                ]);
                $this->categories[] = $childCat;
            }
        }

        $this->command->info('✓ ' . count($this->categories) . ' categories seeded');
    }

    private function seedAttributes()
    {
        $this->command->info('🎨 Seeding product attributes...');

        // Color
        $color = ProductAttribute::create([
            'name' => 'Color', 'slug' => 'color', 'type' => 'select',
            'use_for_variants' => true, 'is_filterable' => true, 'sort_order' => 1,
        ]);
        foreach (['Red', 'Blue', 'Green', 'Yellow', 'Pink', 'Purple', 'Orange', 'Black', 'White',
                  'Navy', 'Gray', 'Brown', 'Teal', 'Multicolor'] as $i => $val) {
            ProductAttributeValue::create([
                'product_attribute_id' => $color->id,
                'value' => $val,
                'sort_order' => $i + 1,
            ]);
        }
        $this->attributes['color'] = $color;

        // Size
        $size = ProductAttribute::create([
            'name' => 'Size', 'slug' => 'size', 'type' => 'select',
            'use_for_variants' => true, 'is_filterable' => true, 'sort_order' => 2,
        ]);
        foreach (['Newborn', '0-3M', '3-6M', '6-9M', '9-12M', '12-18M', '18-24M',
                  '2T', '3T', '4T', '5T', '6', '7', '8', '10', '12', '14',
                  'XS', 'S', 'M', 'L', 'XL'] as $i => $val) {
            ProductAttributeValue::create([
                'product_attribute_id' => $size->id,
                'value' => $val,
                'sort_order' => $i + 1,
            ]);
        }
        $this->attributes['size'] = $size;

        // Age Range
        $age = ProductAttribute::create([
            'name' => 'Age Range', 'slug' => 'age-range', 'type' => 'select',
            'is_filterable' => true, 'sort_order' => 3,
        ]);
        foreach (['0-6 months', '6-12 months', '1-2 years', '2-4 years', '4-7 years',
                  '7-10 years', '10-13 years', '13+ years'] as $i => $val) {
            ProductAttributeValue::create([
                'product_attribute_id' => $age->id,
                'value' => $val,
                'sort_order' => $i + 1,
            ]);
        }
        $this->attributes['age'] = $age;

        // Gender
        $gender = ProductAttribute::create([
            'name' => 'Gender', 'slug' => 'gender', 'type' => 'select',
            'is_filterable' => true, 'sort_order' => 4,
        ]);
        foreach (['Boys', 'Girls', 'Unisex'] as $i => $val) {
            ProductAttributeValue::create([
                'product_attribute_id' => $gender->id,
                'value' => $val,
                'sort_order' => $i + 1,
            ]);
        }
        $this->attributes['gender'] = $gender;

        // Material
        $material = ProductAttribute::create([
            'name' => 'Material', 'slug' => 'material', 'type' => 'select',
            'is_filterable' => true, 'sort_order' => 5,
        ]);
        foreach (['Cotton', 'Polyester', 'Plastic', 'Wood', 'Metal', 'Fabric',
                  'Silicone', 'Rubber', 'Plush'] as $i => $val) {
            ProductAttributeValue::create([
                'product_attribute_id' => $material->id,
                'value' => $val,
                'sort_order' => $i + 1,
            ]);
        }
        $this->attributes['material'] = $material;

        // Pattern
        $pattern = ProductAttribute::create([
            'name' => 'Pattern', 'slug' => 'pattern', 'type' => 'select',
            'use_for_variants' => false, 'is_filterable' => true, 'sort_order' => 6,
        ]);
        foreach (['Solid', 'Striped', 'Polka Dot', 'Floral', 'Animal Print',
                  'Character', 'Geometric'] as $i => $val) {
            ProductAttributeValue::create([
                'product_attribute_id' => $pattern->id,
                'value' => $val,
                'sort_order' => $i + 1,
            ]);
        }
        $this->attributes['pattern'] = $pattern;

        $this->command->info('✓ ' . count($this->attributes) . ' attributes with values seeded');
    }

    private function assignAttributesToCategories()
    {
        $this->command->info('🔗 Assigning attributes to categories...');

        if (!Schema::hasTable('category_attributes')) {
            $this->command->warn('⚠ category_attributes table not found');
            return;
        }

        $attributeMap = [
            'Clothing' => ['color', 'size', 'gender', 'material', 'pattern'],
            'Toys' => ['color', 'age', 'material', 'gender'],
            'Baby' => ['size', 'age', 'color', 'material'],
            'Books' => ['age'],
            'Food' => ['age'],
            'Outdoor' => ['age', 'color'],
            'Room' => ['color', 'size'],
            'Party' => ['color', 'age'],
        ];

        $childCategories = collect($this->categories)->filter(fn($c) => $c->parent_id !== null);

        foreach ($childCategories as $category) {
            $attrs = [];
            foreach ($attributeMap as $key => $attrSlugs) {
                if (stripos($category->name, $key) !== false || stripos($category->parent->name ?? '', $key) !== false) {
                    foreach ($attrSlugs as $slug) {
                        if (isset($this->attributes[$slug])) {
                            $attrs[] = $this->attributes[$slug]->id;
                        }
                    }
                    break;
                }
            }

            if (empty($attrs)) {
                $attrs = [
                    $this->attributes['color']->id,
                    $this->attributes['age']->id,
                ];
            }

            $category->attributes()->sync(array_unique($attrs));
        }

        $this->command->info('✓ Attributes assigned to categories');
    }

    private function seedProducts()
    {
        $this->command->info('🛍️  Seeding products...');

        $templates = $this->getProductTemplates();
        $childCategories = collect($this->categories)->filter(fn($c) => $c->parent_id !== null)->values();

        $productCount = 0;
        $variantCount = 0;

        foreach ($templates as $template) {
            $category = $childCategories->firstWhere('name', 'like', '%' . $template['category'] . '%')
                     ?? $childCategories->random();
            $brand = collect($this->brands)->firstWhere('name', 'like', '%' . $template['brand'] . '%')
                  ?? collect($this->brands)->random();

            // Create multiple variations per template
            for ($v = 0; $v < rand(2, 4); $v++) {
                $variantSuffix = $v > 0 ? ' - ' . ['Style A', 'Style B', 'Style C', 'Style D'][$v] : '';
                $price = $template['price'] * (1 + (rand(-15, 25) / 100));
                $cost = $price * (rand(45, 65) / 100);

                $product = Product::create([
                    'name' => $template['name'] . $variantSuffix,
                    'slug' => Str::slug($template['name'] . $variantSuffix . '-' . uniqid()),
                    'sku' => 'KH-' . strtoupper(Str::random(8)),
                    'barcode' => rand(0, 1) ? '8' . str_pad(rand(0, 999999999999), 12, '0') : null,
                    'description' => $template['description'],
                    'short_description' => $template['short'],
                    'price' => round($price, 2),
                    'cost_price' => round($cost, 2),
                    'discount_price' => rand(0, 100) > 70 ? round($price * (rand(80, 92) / 100), 2) : null,
                    'stock_quantity' => rand(5, 250),
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'product_type' => $template['has_variants'] ? 'variable' : 'simple',
                    'status' => rand(0, 100) > 5 ? 'active' : 'inactive',
                    'is_featured' => rand(0, 100) > 75,
                    'weight' => rand(50, 5000) / 100,
                    'length' => rand(5, 100),
                    'width' => rand(5, 100),
                    'height' => rand(2, 50),
                    'features' => $template['features'] ?? null,
                    'ingredients' => $template['ingredients'] ?? null,
                    'safety_warning' => $template['safety'] ?? null,
                    'meta_title' => $template['name'],
                    'meta_description' => $template['short'],
                ]);

                $this->products[] = $product;
                $productCount++;

                // Create variants if applicable
                if ($template['has_variants']) {
                    $variantCount += $this->createVariantsForProduct($product, $category);
                }
            }

            if ($productCount >= 200) break;
        }

        $this->command->info("✓ {$productCount} products seeded with {$variantCount} variants");
    }

    private function createVariantsForProduct($product, $category)
    {
        $categoryAttrs = $category->attributes()->where('use_for_variants', true)->get();
        if ($categoryAttrs->isEmpty()) return 0;

        $variantData = [];

        // Get color and size if available
        $colorAttr = $categoryAttrs->firstWhere('slug', 'color');
        $sizeAttr = $categoryAttrs->firstWhere('slug', 'size');

        $colors = $colorAttr ? $colorAttr->values()->inRandomOrder()->limit(rand(3, 5))->get() : collect();
        $sizes = $sizeAttr ? $sizeAttr->values()->inRandomOrder()->limit(rand(3, 6))->get() : collect();

        $count = 0;
        if ($colors->isNotEmpty() && $sizes->isNotEmpty()) {
            // Create color x size combinations
            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    $colorCode = strtoupper(Str::slug($color->value, ''));
                    $sizeCode = strtoupper(Str::slug($size->value, ''));
                    $baseSku = $product->sku . '-' . $colorCode . '-' . $sizeCode;
                    $sku = $baseSku . '-' . strtoupper(Str::random(4));
                    while (ProductVariant::where('sku', $sku)->exists()) {
                        $sku = $baseSku . '-' . strtoupper(Str::random(4));
                    }
                    $priceAdj = 1 + (rand(-10, 10) / 100);

                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $sku,
                        'price' => round($product->price * $priceAdj, 2),
                        'cost_price' => round($product->cost_price * $priceAdj, 2),
                        'stock_quantity' => rand(5, 100),
                        'is_active' => true,
                        'is_default' => $count === 0,
                    ]);

                    VariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $colorAttr->id,
                        'product_attribute_value_id' => $color->id,
                    ]);

                    VariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $sizeAttr->id,
                        'product_attribute_value_id' => $size->id,
                    ]);

                    $count++;
                }
            }
        } elseif ($colors->isNotEmpty()) {
            foreach ($colors as $color) {
                $colorCode = strtoupper(Str::slug($color->value, ''));
                $baseSku = $product->sku . '-' . $colorCode;
                $sku = $baseSku . '-' . strtoupper(Str::random(4));
                while (ProductVariant::where('sku', $sku)->exists()) {
                    $sku = $baseSku . '-' . strtoupper(Str::random(4));
                }
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'price' => $product->price,
                    'cost_price' => $product->cost_price,
                    'stock_quantity' => rand(10, 100),
                    'is_active' => true,
                    'is_default' => $count === 0,
                ]);

                VariantAttribute::create([
                    'product_variant_id' => $variant->id,
                    'product_attribute_id' => $colorAttr->id,
                    'product_attribute_value_id' => $color->id,
                ]);
                $count++;
            }
        }

        return $count;
    }

    private function seedOrders()
    {
        $this->command->info('📦 Seeding orders...');

        if (!Schema::hasTable('orders')) {
            $this->command->warn('⚠ orders table not found');
            return;
        }

        $customers = collect($this->users)->filter(fn($u) => !$u->is_admin);
        $statuses = ['pending', 'processing', 'completed', 'cancelled', 'refunded'];
        $orderCount = 0;

        // Create orders over the past 6 months
        for ($i = 0; $i < 150; $i++) {
            $customer = $customers->random();
            $status = $statuses[array_rand($statuses)];
            $orderDate = Carbon::now()->subDays(rand(1, 180));

            $order = Order::create([
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => fake()->phoneNumber(),
                'address_line' => fake()->streetAddress(),
                'city' => fake()->city(),
                'postal_code' => fake()->postcode(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => $status,
                'subtotal' => 0,
                'tax_amount' => 0,
                'payment_method' => ['cash_on_delivery', 'card', 'mobile_banking'][rand(0, 2)],
                'notes' => rand(0, 1) ? fake()->sentence() : null,
                'total_amount' => 0,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Add order items
            $itemCount = rand(1, 5);
            $subtotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $product = collect($this->products)->random();
                $quantity = rand(1, 3);
                $price = $product->discount_price ?? $product->price;
                $unitCost = $product->cost_price ?? ($product->price * 0.6);
                $itemTotal = $price * $quantity;
                $subtotal += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => null,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'unit_cost' => $unitCost,
                    'total_price' => $itemTotal,
                ]);
            }

            $tax = $subtotal * 0.05;
            $total = $subtotal + $tax;

            $order->update([
                'subtotal' => round($subtotal, 2),
                'tax_amount' => round($tax, 2),
                'total_amount' => round($total, 2),
            ]);

            $orderCount++;
        }

        $this->command->info("✓ {$orderCount} orders seeded");
    }

    private function seedExpensesAndInvestments()
    {
        $this->command->info('💰 Seeding expenses and investments...');

        // Expenses
        if (Schema::hasTable('expenses')) {
            $categoryIds = Schema::hasTable('expense_categories')
                ? DB::table('expense_categories')->pluck('id')->all()
                : [];
            $expenseTitles = ['Rent', 'Utilities', 'Salaries', 'Marketing', 'Supplies', 'Transportation', 'Miscellaneous'];
            $expenseStatuses = ['pending', 'approved', 'rejected'];

            for ($i = 0; $i < 50; $i++) {
                DB::table('expenses')->insert([
                    'title' => $expenseTitles[array_rand($expenseTitles)],
                    'description' => fake()->sentence(),
                    'amount' => rand(1000, 50000),
                    'expense_date' => Carbon::now()->subDays(rand(1, 180))->format('Y-m-d'),
                    'category_id' => !empty($categoryIds) ? $categoryIds[array_rand($categoryIds)] : null,
                    'partner_id' => null,
                    'status' => $expenseStatuses[array_rand($expenseStatuses)],
                    'created_by' => $this->users[0]->id ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $this->command->info('✓ 50 expenses seeded');
        }

        // Investments
        if (Schema::hasTable('investments')) {
            $types = ['inventory', 'equipment', 'property', 'marketing', 'research', 'expansion', 'other'];
            $sources = ['Owner Capital', 'Bank Loan', 'Investor Funding', 'Retained Earnings'];
            $statuses = ['active', 'completed', 'sold'];

            for ($i = 0; $i < 15; $i++) {
                DB::table('investments')->insert([
                    'title' => $sources[array_rand($sources)],
                    'description' => fake()->sentence(),
                    'amount' => rand(50000, 500000),
                    'type' => $types[array_rand($types)],
                    'current_value' => rand(40000, 600000),
                    'investment_date' => Carbon::now()->subDays(rand(1, 365))->format('Y-m-d'),
                    'status' => $statuses[array_rand($statuses)],
                    'notes' => rand(0, 1) ? fake()->sentence() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $this->command->info('✓ 15 investments seeded');
        }
    }

    private function getProductTemplates()
    {
        return [
            // Toys
            ['name' => 'Building Block Set', 'category' => 'Building', 'brand' => 'LEGO', 'price' => 2999, 'description' => 'Creative building blocks set with 500+ pieces. Develops motor skills and creativity.', 'short' => 'Building blocks set', 'has_variants' => false, 'age_range' => '4-7 years', 'features' => 'BPA-free plastic, Compatible with major brands', 'safety' => 'Choking hazard - small parts. Not for children under 3.'],
            ['name' => 'Action Figure Superhero', 'category' => 'Action', 'brand' => 'Marvel', 'price' => 1299, 'description' => 'Poseable action figure with multiple accessories. Premium quality collectible toy.', 'short' => 'Superhero action figure', 'has_variants' => false, 'age_range' => '4-7 years'],
            ['name' => 'Dollhouse Mansion', 'category' => 'Dolls', 'brand' => 'Barbie', 'price' => 8999, 'description' => 'Three-story dollhouse with furniture and accessories included.', 'short' => 'Deluxe dollhouse', 'has_variants' => false, 'age_range' => '4-7 years'],
            ['name' => 'Remote Control Racing Car', 'category' => 'Remote', 'brand' => 'Hot Wheels', 'price' => 3499, 'description' => 'High-speed RC car with rechargeable battery and remote control.', 'short' => 'RC racing car', 'has_variants' => true, 'age_range' => '7-10 years'],
            ['name' => 'Educational Puzzle Set', 'category' => 'Puzzles', 'brand' => 'Melissa & Doug', 'price' => 1499, 'description' => '100-piece jigsaw puzzle perfect for developing problem-solving skills.', 'short' => 'Educational puzzle', 'has_variants' => false, 'age_range' => '4-7 years'],
            ['name' => 'Plush Teddy Bear', 'category' => 'Stuffed', 'brand' => 'Disney', 'price' => 1699, 'description' => 'Soft and cuddly teddy bear, hypoallergenic filling, machine washable.', 'short' => 'Soft plush bear', 'has_variants' => true, 'age_range' => '0-6 months'],
            ['name' => 'Art & Craft Supplies Kit', 'category' => 'Arts', 'brand' => 'Crayola', 'price' => 2299, 'description' => 'Complete art set with crayons, markers, colored pencils, paper and more.', 'short' => 'Art supplies kit', 'has_variants' => false, 'age_range' => '4-7 years'],
            ['name' => 'Play Kitchen Set', 'category' => 'Pretend', 'brand' => 'Little Tikes', 'price' => 6999, 'description' => 'Interactive kitchen playset with realistic sounds and accessories.', 'short' => 'Kitchen playset', 'has_variants' => false, 'age_range' => '2-4 years'],

            // Clothing
            ['name' => 'Cotton T-Shirt', 'category' => 'Clothing', 'brand' => "Carter's", 'price' => 899, 'description' => '100% cotton comfortable t-shirt, breathable and soft fabric.', 'short' => 'Kids cotton t-shirt', 'has_variants' => true, 'age_range' => '2-4 years'],
            ['name' => 'Denim Jeans', 'category' => 'Clothing', 'brand' => "OshKosh B'gosh", 'price' => 1999, 'description' => 'Durable denim jeans with adjustable waist, perfect for active kids.', 'short' => 'Kids denim jeans', 'has_variants' => true, 'age_range' => '4-7 years'],
            ['name' => 'Princess Dress', 'category' => 'Girls', 'brand' => 'Disney', 'price' => 2499, 'description' => 'Beautiful princess dress perfect for parties and special occasions.', 'short' => 'Princess party dress', 'has_variants' => true, 'age_range' => '4-7 years'],
            ['name' => 'Pajama Set', 'category' => 'Sleepwear', 'brand' => "Carter's", 'price' => 1599, 'description' => 'Comfortable two-piece pajama set, 100% cotton, machine washable.', 'short' => 'Kids pajama set', 'has_variants' => true, 'age_range' => '2-4 years'],
            ['name' => 'Winter Jacket', 'category' => 'Outerwear', 'brand' => 'Nike Kids', 'price' => 4499, 'description' => 'Water-resistant winter jacket with hood and warm insulation.', 'short' => 'Winter puffer jacket', 'has_variants' => true, 'age_range' => '7-10 years'],
            ['name' => 'Athletic Sneakers', 'category' => 'Shoes', 'brand' => 'Adidas Kids', 'price' => 3999, 'description' => 'Comfortable athletic shoes with non-slip sole and breathable mesh.', 'short' => 'Kids sneakers', 'has_variants' => true, 'age_range' => '7-10 years'],
            ['name' => 'Summer Shorts', 'category' => 'Boys', 'brand' => "The Children's Place", 'price' => 1299, 'description' => 'Lightweight cotton shorts perfect for summer play.', 'short' => 'Boys shorts', 'has_variants' => true, 'age_range' => '4-7 years'],
            ['name' => 'Hooded Sweatshirt', 'category' => 'Boys', 'brand' => 'Gap Kids', 'price' => 2299, 'description' => 'Cozy hooded sweatshirt with front pocket, perfect for cool days.', 'short' => 'Kids hoodie', 'has_variants' => true, 'age_range' => '7-10 years'],

            // Baby Products
            ['name' => 'Baby Bottle Set', 'category' => 'Feeding', 'brand' => 'Gerber', 'price' => 1899, 'description' => 'Anti-colic baby bottles with slow-flow nipples, BPA-free, dishwasher safe.', 'short' => 'Baby bottle 3-pack', 'has_variants' => false, 'age_range' => '0-6 months'],
            ['name' => 'Diaper Bag Backpack', 'category' => 'Gear', 'brand' => 'Graco', 'price' => 4999, 'description' => 'Multi-compartment diaper bag with insulated pockets and changing pad.', 'short' => 'Diaper backpack', 'has_variants' => true, 'age_range' => '0-6 months'],
            ['name' => 'Baby Monitor Camera', 'category' => 'Electronics', 'brand' => 'Baby Einstein', 'price' => 12999, 'description' => 'HD video baby monitor with night vision, two-way audio and smartphone app.', 'short' => 'Video baby monitor', 'has_variants' => false, 'age_range' => '0-6 months'],
            ['name' => 'Pacifier Set', 'category' => 'Care', 'brand' => 'Johnson\'s Baby', 'price' => 699, 'description' => 'Orthodontic pacifiers, BPA-free silicone, comes with protective case.', 'short' => 'Baby pacifiers', 'has_variants' => false, 'age_range' => '0-6 months'],
            ['name' => 'Baby Blanket Fleece', 'category' => 'Gear', 'brand' => "Carter's", 'price' => 1999, 'description' => 'Soft fleece baby blanket, hypoallergenic, machine washable.', 'short' => 'Fleece baby blanket', 'has_variants' => true, 'age_range' => '0-6 months'],
            ['name' => 'Baby Bath Tub', 'category' => 'Care', 'brand' => 'Fisher-Price', 'price' => 2499, 'description' => 'Ergonomic baby bathtub with temperature indicator and drain plug.', 'short' => 'Baby bath tub', 'has_variants' => false, 'age_range' => '0-6 months'],

            // Books
            ['name' => 'Picture Storybook', 'category' => 'Picture', 'brand' => 'Scholastic', 'price' => 799, 'description' => 'Colorful illustrated storybook with engaging narratives for young readers.', 'short' => 'Children\'s storybook', 'has_variants' => false, 'age_range' => '2-4 years'],
            ['name' => 'Board Book Set', 'category' => 'Board', 'brand' => 'Usborne', 'price' => 1299, 'description' => 'Durable board books perfect for toddlers, set of 3 books.', 'short' => 'Board books 3-pack', 'has_variants' => false, 'age_range' => '0-6 months'],
            ['name' => 'Activity & Coloring Book', 'category' => 'Activity', 'brand' => 'Crayola', 'price' => 599, 'description' => 'Fun activities, mazes, puzzles and coloring pages for hours of entertainment.', 'short' => 'Activity book', 'has_variants' => false, 'age_range' => '4-7 years'],
            ['name' => 'Educational Workbook', 'category' => 'Educational', 'brand' => 'Scholastic', 'price' => 899, 'description' => 'Learn letters, numbers and shapes through fun exercises and activities.', 'short' => 'Learning workbook', 'has_variants' => false, 'age_range' => '4-7 years'],

            // Food & Nutrition
            ['name' => 'Baby Formula Powder', 'category' => 'Formula', 'brand' => 'Gerber', 'price' => 3999, 'description' => 'Nutritionally complete infant formula with DHA and probiotics.', 'short' => 'Baby formula', 'has_variants' => false, 'age_range' => '0-6 months', 'ingredients' => 'Milk protein, vegetable oils, vitamins, minerals'],
            ['name' => 'Organic Baby Food Pouches', 'category' => 'Food', 'brand' => 'Happy Baby', 'price' => 799, 'description' => 'Organic fruit and vegetable puree pouches, no added sugar or preservatives.', 'short' => 'Baby food pouches', 'has_variants' => false, 'age_range' => '6-12 months', 'ingredients' => 'Organic fruits and vegetables'],
            ['name' => 'Kids Multivitamin Gummies', 'category' => 'Vitamins', 'brand' => 'Earth\'s Best', 'price' => 1299, 'description' => 'Delicious gummy vitamins with essential nutrients for growing kids.', 'short' => 'Kids vitamins', 'has_variants' => false, 'age_range' => '2-4 years', 'ingredients' => 'Vitamins A, C, D, E, B-complex'],
            ['name' => 'Toddler Snack Crackers', 'category' => 'Snacks', 'brand' => 'Gerber', 'price' => 499, 'description' => 'Wholesome baked crackers perfect for little hands, no artificial flavors.', 'short' => 'Toddler crackers', 'has_variants' => false, 'age_range' => '1-2 years', 'ingredients' => 'Whole wheat flour, cheese, vitamins'],

            // Outdoor & Sports
            ['name' => 'Kids Bicycle', 'category' => 'Bikes', 'brand' => 'Step2', 'price' => 14999, 'description' => 'Kids bicycle with training wheels, adjustable seat and safety features.', 'short' => 'Kids bike', 'has_variants' => true, 'age_range' => '4-7 years'],
            ['name' => 'Kick Scooter', 'category' => 'Ride-Ons', 'brand' => 'Razor', 'price' => 5999, 'description' => '3-wheel kick scooter with LED lights and adjustable handlebar.', 'short' => 'Kids scooter', 'has_variants' => true, 'age_range' => '4-7 years'],
            ['name' => 'Soccer Ball Youth', 'category' => 'Sports', 'brand' => 'Adidas Kids', 'price' => 1799, 'description' => 'Durable youth size 3 soccer ball, perfect for practice and play.', 'short' => 'Kids soccer ball', 'has_variants' => false, 'age_range' => '4-7 years'],
            ['name' => 'Basketball Youth', 'category' => 'Sports', 'brand' => 'Nike Kids', 'price' => 1999, 'description' => 'Youth size 5 basketball with good grip and bounce.', 'short' => 'Kids basketball', 'has_variants' => false, 'age_range' => '7-10 years'],
            ['name' => 'Jump Rope', 'category' => 'Outdoor', 'brand' => 'Melissa & Doug', 'price' => 699, 'description' => 'Adjustable jump rope with comfortable handles, promotes active play.', 'short' => 'Kids jump rope', 'has_variants' => false, 'age_range' => '4-7 years'],
            ['name' => 'Play Tent', 'category' => 'Outdoor', 'brand' => 'Little Tikes', 'price' => 3499, 'description' => 'Pop-up play tent for indoor and outdoor fun, easy setup.', 'short' => 'Kids play tent', 'has_variants' => true, 'age_range' => '2-4 years'],
        ];
    }
}
