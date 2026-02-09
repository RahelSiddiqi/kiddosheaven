<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Catalog;
use App\Models\CatalogType;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Partner;
use App\Models\PartnerPayment;
use App\Models\PartnerCalculation;
use App\Models\Investor;
use App\Models\Investment;
use App\Models\Role;
use App\Models\Permission;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyTransaction;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\CmsPage;
use App\Models\Review;
use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('Starting comprehensive database seeding...');

        // Seed core data first
        $this->seedSettings();
        $this->seedCatalogTypes();
        $this->seedCatalogs();
        $this->seedBrands();
        $this->seedProductAttributes();
        $this->seedCategorySpecificAttributes();
        $this->seedCatalogTypeAttributes();
        $this->seedRoles();
        $this->seedUsers();
        $this->seedProducts();

        // Seed business data
        $this->seedPartners();
        $this->seedInvestors();
        $this->seedOrders();
        $this->seedCoupons();
        $this->seedFlashSales();
        $this->seedCmsPages();
        $this->seedReviews();
        $this->seedExpenseCategories();
        $this->seedExpenses();
        $this->seedLoyaltyPrograms();

        // Seed inventory data
        $this->seedPurchaseBatches();
        $this->seedInventoryMovements();

        // Note: Financial transactions and partner/ investor tables have complex schemas
        // that require additional seeding logic. These can be seeded separately if needed.
        // $this->seedCapitalAccounts();
        // $this->seedFinancialTransactions();
        // $this->seedPartnerPayments();
        // $this->seedPartnerCalculations();
        // $this->seedInvestments();

        $this->command->info('Database seeding completed successfully!');
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => "Kiddo's Heaven"],
            ['key' => 'site_email', 'value' => 'hello@kiddosheaven.com'],
            ['key' => 'site_phone', 'value' => '+880 1234 567890'],
            ['key' => 'site_address', 'value' => '123 Toy Street, Happyville, Dhaka-1000'],
            ['key' => 'site_description', 'value' => 'Playful toys for happy kids. Quality toys, games, and gifts for children of all ages.'],
            ['key' => 'currency', 'value' => 'BDT'],
            ['key' => 'tax_rate', 'value' => '0'],
            ['key' => 'free_shipping_threshold', 'value' => '50'],
            ['key' => 'inventory_costing_method', 'value' => 'fifo'],
        ];
        DB::table('settings')->upsert($settings, 'key', ['value']);
        $this->command->info('Settings seeded');
    }

    private function seedCatalogTypes(): void
    {
        $this->call(CatalogTypesSeeder::class);

        // Run the foreign key migration after seeding catalog types
        $this->command->call('migrate', ['--path' => 'database/migrations/2026_03_01_000002_add_catalog_type_foreign_key.php', '--force' => true]);

        $this->command->info('Catalog types seeded and foreign key added');
    }

    private function seedCatalogs(): void
    {
        $catalogs = [
            ['name' => 'Wooden Toys', 'type' => 'toys', 'description' => 'Natural wooden toys for imaginative play', 'show_on_home' => true, 'created_at' => now()->subDays(10)],
            ['name' => 'Plush Toys', 'type' => 'toys', 'description' => 'Soft and cuddly stuffed animals', 'show_on_home' => true, 'created_at' => now()->subDays(9)],
            ['name' => 'Educational', 'type' => 'toys', 'description' => 'Toys that teach and inspire', 'show_on_home' => true, 'created_at' => now()->subDays(8)],
            ['name' => 'Building Blocks', 'type' => 'toys', 'description' => 'Stack, build, and create', 'show_on_home' => false, 'created_at' => now()->subDays(7)],
            ['name' => 'Art & Craft', 'type' => 'toys', 'description' => 'Creative supplies for little artists', 'show_on_home' => false, 'created_at' => now()->subDays(6)],
            ['name' => 'Outdoor', 'type' => 'toys', 'description' => 'Fun for fresh air adventures', 'show_on_home' => false, 'created_at' => now()->subDays(5)],
            ['name' => 'Baby Toys', 'type' => 'baby', 'description' => 'Safe toys for infants', 'show_on_home' => true, 'created_at' => now()->subDays(4)],
            ['name' => 'Remote Control', 'type' => 'toys', 'description' => 'RC cars, drones, and more', 'show_on_home' => true, 'created_at' => now()->subDays(3)],
            ['name' => 'Board Games', 'type' => 'toys', 'description' => 'Family fun for game night', 'show_on_home' => false, 'created_at' => now()->subDays(2)],
            ['name' => 'Puzzles', 'type' => 'puzzles', 'description' => 'Challenge your mind', 'show_on_home' => true, 'created_at' => now()->subDays(1)],
        ];
        DB::table('catalogs')->upsert($catalogs, 'name', ['type', 'description', 'show_on_home', 'created_at']);
        $this->command->info('Catalogs seeded');
    }

    private function seedBrands(): void
    {
        $brands = [
            ['name' => 'Wooden Wonders', 'slug' => 'wooden-wonders', 'description' => 'Premium wooden toys', 'is_active' => true, 'created_at' => now()],
            ['name' => 'Soft Friends', 'slug' => 'soft-friends', 'description' => 'Quality plush toys', 'is_active' => true, 'created_at' => now()],
            ['name' => 'SmartPlay', 'slug' => 'smartplay', 'description' => 'Educational toys for growing minds', 'is_active' => true, 'created_at' => now()],
            ['name' => 'BlockMaster', 'slug' => 'blockmaster', 'description' => 'Building blocks and construction sets', 'is_active' => true, 'created_at' => now()],
            ['name' => 'BabyGear', 'slug' => 'babygear', 'description' => 'Safe baby products', 'is_active' => true, 'created_at' => now()],
        ];
        DB::table('brands')->upsert($brands, 'slug', ['description', 'is_active', 'created_at']);
        $this->command->info('Brands seeded');
    }

    private function seedProductAttributes(): void
    {
        $attributes = [
            ['name' => 'Age Range', 'slug' => 'age-range', 'type' => 'select', 'is_required' => true, 'is_filterable' => true, 'sort_order' => 1, 'description' => 'Recommended age range for the product'],
            ['name' => 'Material', 'slug' => 'material', 'type' => 'select', 'is_required' => true, 'is_filterable' => true, 'sort_order' => 2, 'description' => 'Primary material used'],
            ['name' => 'Color', 'slug' => 'color', 'type' => 'select', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 3, 'description' => 'Product color'],
            ['name' => 'Battery Required', 'slug' => 'battery-required', 'type' => 'boolean', 'is_required' => false, 'is_filterable' => false, 'sort_order' => 4, 'description' => 'Whether batteries are required'],
            ['name' => 'Weight', 'slug' => 'weight', 'type' => 'text', 'is_required' => false, 'is_filterable' => false, 'sort_order' => 5, 'description' => 'Product weight'],
            ['name' => 'Dimensions', 'slug' => 'dimensions', 'type' => 'text', 'is_required' => false, 'is_filterable' => false, 'sort_order' => 6, 'description' => 'Product dimensions'],
            ['name' => 'Safety Certification', 'slug' => 'safety-certification', 'type' => 'select', 'is_required' => true, 'is_filterable' => true, 'sort_order' => 7, 'description' => 'Safety certification standard'],
            ['name' => 'Washable', 'slug' => 'washable', 'type' => 'boolean', 'is_required' => false, 'is_filterable' => false, 'sort_order' => 8, 'description' => 'Whether the product is washable'],
            ['name' => 'Skill Development', 'slug' => 'skill-development', 'type' => 'multiselect', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 9, 'description' => 'Skills developed through play'],
            ['name' => 'Gender', 'slug' => 'gender', 'type' => 'select', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 10, 'description' => 'Target gender'],
            ['name' => 'Piece Count', 'slug' => 'piece-count', 'type' => 'number', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 11, 'description' => 'Number of pieces in the puzzle/game'],
            ['name' => 'Difficulty Level', 'slug' => 'difficulty-level', 'type' => 'select', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 12, 'description' => 'Difficulty level of the puzzle/game'],
            ['name' => 'Size', 'slug' => 'size', 'type' => 'select', 'is_required' => false, 'is_filterable' => true, 'sort_order' => 13, 'description' => 'Product size'],
        ];
        DB::table('product_attributes')->upsert($attributes, 'slug', ['type', 'is_required', 'is_filterable', 'sort_order', 'description', 'created_at']);
        $this->command->info('Product attributes seeded');
    }

    private function seedCategorySpecificAttributes(): void
    {
        // Call the PuzzleAttributesSeeder
        $this->call(PuzzleAttributesSeeder::class);
        $this->command->info('Category-specific attributes seeded');
    }

    private function seedCatalogTypeAttributes(): void
    {
        // Link product attributes to catalog types based on the type's nature
        $typeAttributeMappings = [
            'toys' => [
                // All toys can have these attributes
                'age-range',
                'material',
                'color',
                'battery-required',
                'safety-certification',
                'skill-development',
                'gender',
            ],
            'puzzles' => [
                // Puzzles specific attributes
                'age-range',
                'material',
                'piece-count',
                'difficulty-level',
                'safety-certification',
                'skill-development',
            ],
            'clothing' => [
                // Clothing specific attributes
                'size',
                'color',
                'material',
                'gender',
                'age-range',
                'washable',
            ],
            'general' => [
                // General can have most attributes
                'age-range',
                'material',
                'color',
                'weight',
                'dimensions',
                'safety-certification',
            ],
        ];

        foreach ($typeAttributeMappings as $typeSlug => $attributeSlugs) {
            $catalogType = CatalogType::where('slug', $typeSlug)->first();
            if (!$catalogType) {
                continue;
            }

            $attributeIds = ProductAttribute::whereIn('slug', $attributeSlugs)->pluck('id')->toArray();

            $syncData = [];
            foreach ($attributeIds as $index => $attrId) {
                $syncData[$attrId] = [
                    'sort_order' => $index + 1,
                    'is_required' => in_array($attributeSlugs[$index], ['age-range', 'material', 'safety-certification']),
                ];
            }

            $catalogType->attributes()->sync($syncData);
        }

        $this->command->info('Catalog type attributes seeded');
    }

    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full system access', 'is_default' => false],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Administrative access', 'is_default' => false],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Management access', 'is_default' => false],
            ['name' => 'Support', 'slug' => 'support', 'description' => 'Customer support access', 'is_default' => false],
        ];
        DB::table('roles')->upsert($roles, 'slug', ['description', 'is_default', 'created_at']);
        $this->command->info('Roles seeded');
    }

    private function seedUsers(): void
    {
        // Seed admin user
        User::firstOrCreate(
            ['email' => 'admin@kiddosheaven.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'is_active' => true,
            ]
        );

        // Seed sample customers
        $customers = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => Hash::make('password')],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'password' => Hash::make('password')],
            ['name' => 'Bob Wilson', 'email' => 'bob@example.com', 'password' => Hash::make('password')],
            ['name' => 'Alice Brown', 'email' => 'alice@example.com', 'password' => Hash::make('password')],
            ['name' => 'Charlie Davis', 'email' => 'charlie@example.com', 'password' => Hash::make('password')],
        ];

        foreach ($customers as $customer) {
            User::firstOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'password' => $customer['password'],
                    'is_admin' => false,
                    'is_active' => true,
                ]
            );
        }
        $this->command->info('Users seeded');
    }

    private function seedProducts(): void
    {
        $products = [
            [
                'name' => 'Wooden Train Set',
                'slug' => 'wooden-train-set',
                'description' => 'Colorful wooden train with tracks and accessories. Perfect for developing imagination and motor skills.',
                'short_description' => 'Colorful wooden train set',
                'price' => 2999,
                'cost_price' => 1500,
                'discount_price' => null,
                'sku' => 'WT-001',
                'stock_quantity' => 25,
                'sold_count' => 45,
                'catalog_id' => 1,
                'brand_id' => 1,
                'is_featured' => true,
                'status' => 'active',
                'meta_title' => 'Wooden Train Set - Kiddo\'s Heaven',
                'meta_description' => 'Premium wooden train set for kids',
                'created_at' => now()->subDays(30),
            ],
            [
                'name' => 'Stacking Rainbow',
                'slug' => 'stacking-rainbow',
                'description' => 'Classic wooden rainbow stacker for fine motor skills development.',
                'short_description' => 'Wooden rainbow stacker',
                'price' => 1999,
                'cost_price' => 800,
                'discount_price' => null,
                'sku' => 'SR-001',
                'stock_quantity' => 30,
                'sold_count' => 67,
                'catalog_id' => 1,
                'brand_id' => 1,
                'is_featured' => true,
                'status' => 'active',
                'meta_title' => 'Stacking Rainbow - Kiddo\'s Heaven',
                'meta_description' => 'Classic wooden rainbow stacker',
                'created_at' => now()->subDays(28),
            ],
            [
                'name' => 'Cuddly Bear',
                'slug' => 'cuddly-bear',
                'description' => 'Soft and huggable teddy bear made with premium materials.',
                'short_description' => 'Soft teddy bear',
                'price' => 2499,
                'cost_price' => 900,
                'discount_price' => null,
                'sku' => 'CB-001',
                'stock_quantity' => 40,
                'sold_count' => 89,
                'catalog_id' => 2,
                'brand_id' => 2,
                'is_featured' => true,
                'status' => 'active',
                'meta_title' => 'Cuddly Bear - Kiddo\'s Heaven',
                'meta_description' => 'Soft and huggable teddy bear',
                'created_at' => now()->subDays(25),
            ],
            [
                'name' => 'Bunny Plush',
                'slug' => 'bunny-plush',
                'description' => 'Adorable pink bunny plush with floppy ears.',
                'short_description' => 'Adorable pink bunny plush',
                'price' => 1899,
                'cost_price' => 650,
                'discount_price' => null,
                'sku' => 'BP-001',
                'stock_quantity' => 35,
                'sold_count' => 56,
                'catalog_id' => 2,
                'brand_id' => 2,
                'is_featured' => false,
                'status' => 'active',
                'meta_title' => 'Bunny Plush - Kiddo\'s Heaven',
                'meta_description' => 'Adorable pink bunny plush',
                'created_at' => now()->subDays(23),
            ],
            [
                'name' => 'Math Learning Set',
                'slug' => 'math-learning-set',
                'description' => 'Complete math learning kit for beginners.',
                'short_description' => 'Math learning kit',
                'price' => 3499,
                'cost_price' => 1800,
                'discount_price' => null,
                'sku' => 'ML-001',
                'stock_quantity' => 20,
                'sold_count' => 34,
                'catalog_id' => 3,
                'brand_id' => 3,
                'is_featured' => true,
                'status' => 'active',
                'meta_title' => 'Math Learning Set - Kiddo\'s Heaven',
                'meta_description' => 'Complete math learning kit',
                'created_at' => now()->subDays(20),
            ],
            [
                'name' => 'Alphabet Puzzle',
                'slug' => 'alphabet-puzzle',
                'description' => 'Learn ABCs with this beautifully crafted wooden puzzle.',
                'short_description' => 'Wooden alphabet puzzle',
                'price' => 1499,
                'cost_price' => 500,
                'discount_price' => null,
                'sku' => 'AP-001',
                'stock_quantity' => 45,
                'sold_count' => 123,
                'catalog_id' => 3,
                'brand_id' => 3,
                'is_featured' => false,
                'status' => 'active',
                'meta_title' => 'Alphabet Puzzle - Kiddo\'s Heaven',
                'meta_description' => 'Learn ABCs with wooden puzzle',
                'created_at' => now()->subDays(18),
            ],
            [
                'name' => 'Mega Block Set',
                'slug' => 'mega-block-set',
                'description' => '100 pieces of colorful building blocks in storage box.',
                'short_description' => '100-piece building blocks',
                'price' => 3999,
                'cost_price' => 2000,
                'discount_price' => null,
                'sku' => 'MB-001',
                'stock_quantity' => 15,
                'sold_count' => 28,
                'catalog_id' => 4,
                'brand_id' => 4,
                'is_featured' => true,
                'status' => 'active',
                'meta_title' => 'Mega Block Set - Kiddo\'s Heaven',
                'meta_description' => '100 pieces of colorful building blocks',
                'created_at' => now()->subDays(15),
            ],
            [
                'name' => 'Colorful Blocks',
                'slug' => 'colorful-blocks',
                'description' => 'Assorted colored building blocks - 50 pieces.',
                'short_description' => '50-piece colored blocks',
                'price' => 2499,
                'cost_price' => 1000,
                'discount_price' => null,
                'sku' => 'CB-002',
                'stock_quantity' => 8,
                'sold_count' => 67,
                'catalog_id' => 4,
                'brand_id' => 4,
                'is_featured' => false,
                'status' => 'active',
                'meta_title' => 'Colorful Blocks - Kiddo\'s Heaven',
                'meta_description' => 'Assorted colored building blocks',
                'created_at' => now()->subDays(12),
            ],
            [
                'name' => 'Baby Activity Gym',
                'slug' => 'baby-activity-gym',
                'description' => 'Soft activity mat for tummy time fun.',
                'short_description' => 'Baby activity mat',
                'price' => 4499,
                'cost_price' => 2200,
                'discount_price' => null,
                'sku' => 'BAG-001',
                'stock_quantity' => 12,
                'sold_count' => 19,
                'catalog_id' => 7,
                'brand_id' => 5,
                'is_featured' => true,
                'status' => 'active',
                'meta_title' => 'Baby Activity Gym - Kiddo\'s Heaven',
                'meta_description' => 'Soft activity mat for tummy time',
                'created_at' => now()->subDays(10),
            ],
            [
                'name' => 'RC Racing Car',
                'slug' => 'rc-racing-car',
                'description' => 'High speed RC racing car with rechargeable battery.',
                'short_description' => 'RC racing car',
                'price' => 5999,
                'cost_price' => 3000,
                'discount_price' => null,
                'sku' => 'RC-001',
                'stock_quantity' => 18,
                'sold_count' => 25,
                'catalog_id' => 8,
                'brand_id' => 4,
                'is_featured' => true,
                'status' => 'active',
                'meta_title' => 'RC Racing Car - Kiddo\'s Heaven',
                'meta_description' => 'High speed RC racing car',
                'created_at' => now()->subDays(8),
            ],
        ];

        $productData = [];
        foreach ($products as $product) {
            $productData[] = [
                'name' => $product['name'],
                'slug' => $product['slug'],
                'description' => $product['description'],
                'short_description' => $product['short_description'],
                'price' => $product['price'],
                'cost_price' => $product['cost_price'],
                'discount_price' => $product['discount_price'],
                'sku' => $product['sku'],
                'stock_quantity' => $product['stock_quantity'],
                'sold_count' => $product['sold_count'],
                'catalog_id' => $product['catalog_id'],
                'brand_id' => $product['brand_id'],
                'is_featured' => $product['is_featured'],
                'status' => $product['status'],
                'meta_title' => $product['meta_title'],
                'meta_description' => $product['meta_description'],
                'created_at' => $product['created_at'],
                'updated_at' => now(),
            ];
        }
        DB::table('products')->upsert($productData, 'slug', ['description', 'short_description', 'price', 'cost_price', 'discount_price', 'sku', 'stock_quantity', 'sold_count', 'catalog_id', 'brand_id', 'is_featured', 'status', 'meta_title', 'meta_description', 'created_at', 'updated_at']);
        $this->command->info('Products seeded');
    }

    private function seedPartners(): void
    {
        $partners = [
            [
                'name' => 'ToyWorld Distributors',
                'type' => 'supplier',
                'contact_info' => json_encode(['email' => 'orders@toyworld.com', 'phone' => '+8801234567890', 'address' => '123 Trade Center, Dhaka']),
                'commission_rate' => 8.5,
                'status' => 'active',
                'notes' => 'Primary supplier for wooden toys',
                'created_at' => now()->subDays(60),
            ],
            [
                'name' => 'Plush Palace',
                'type' => 'supplier',
                'contact_info' => json_encode(['email' => 'wholesale@plushpalace.com', 'phone' => '+8802345678901', 'address' => '456 Fashion Ave, Chittagong']),
                'commission_rate' => 7.0,
                'status' => 'active',
                'notes' => 'Premium plush toy supplier',
                'created_at' => now()->subDays(55),
            ],
            [
                'name' => 'EduToys Inc',
                'type' => 'supplier',
                'contact_info' => json_encode(['email' => 'business@edutoys.com', 'phone' => '+8803456789012', 'address' => '789 Innovation Park, Sylhet']),
                'commission_rate' => 9.0,
                'status' => 'active',
                'notes' => 'Educational toys specialist',
                'created_at' => now()->subDays(50),
            ],
            [
                'name' => 'Sarah Marketing',
                'type' => 'affiliate',
                'contact_info' => json_encode(['email' => 'sarah@example.com', 'phone' => '+8804567890123']),
                'commission_rate' => 15.0,
                'status' => 'active',
                'notes' => 'Top performing affiliate partner',
                'created_at' => now()->subDays(45),
            ],
            [
                'name' => 'Kids Paradise Co',
                'type' => 'supplier',
                'contact_info' => json_encode(['email' => 'contact@kidsparadise.com', 'phone' => '+8805678901234', 'address' => '321 Market, Dhaka']),
                'commission_rate' => 10.0,
                'status' => 'active',
                'notes' => 'General toy supplier',
                'created_at' => now()->subDays(40),
            ],
        ];
        DB::table('partners')->upsert($partners, 'name', ['type', 'contact_info', 'commission_rate', 'status', 'notes', 'created_at']);
        $this->command->info('Partners seeded');
    }

    private function seedInvestors(): void
    {
        $investors = [
            [
                'name' => 'Ahmed Family Trust',
                'email' => 'ahmed.trust@example.com',
                'phone' => '+8801111222333',
                'address' => '123 Business Tower, Dhaka-1000',
                'nid_or_passport' => 'NID-123456789',
                'total_invested' => 500000,
                'current_value' => 625000,
                'type' => 'individual',
                'contact_person' => 'Khalil Ahmed',
                'contact_email' => 'khalil@example.com',
                'contact_phone' => '+8801111222344',
                'status' => 'active',
                'created_at' => now()->subMonths(6),
            ],
            [
                'name' => 'Growth Ventures Capital',
                'email' => 'info@growthventures.com',
                'phone' => '+8802222333444',
                'address' => '456 Corporate Plaza, Dhaka-1212',
                'nid_or_passport' => 'TIN-987654321',
                'total_invested' => 2000000,
                'current_value' => 2600000,
                'type' => 'venture_capital',
                'contact_person' => 'Maria Rahman',
                'contact_email' => 'maria@growthventures.com',
                'contact_phone' => '+8802222333455',
                'status' => 'active',
                'created_at' => now()->subMonths(12),
            ],
            [
                'name' => 'Angel Investor - Dr. Kamal',
                'email' => 'dr.kamal@example.com',
                'phone' => '+8803333444555',
                'address' => '789 Medical Center, Dhaka-1000',
                'nid_or_passport' => 'NID-111222333',
                'total_invested' => 250000,
                'current_value' => 320000,
                'type' => 'angel',
                'contact_person' => 'Dr. Kamal Hossain',
                'contact_email' => 'dr.kamal@example.com',
                'contact_phone' => '+8803333444555',
                'status' => 'active',
                'created_at' => now()->subMonths(4),
            ],
            [
                'name' => 'Future Kids Foundation',
                'email' => 'invest@futurekids.org',
                'phone' => '+8804444555666',
                'address' => '321 Education Complex, Chittagong-4000',
                'nid_or_passport' => 'NGO-555666777',
                'total_invested' => 750000,
                'current_value' => 920000,
                'type' => 'institution',
                'contact_person' => 'Director',
                'contact_email' => 'director@futurekids.org',
                'contact_phone' => '+8804444555667',
                'status' => 'active',
                'created_at' => now()->subMonths(8),
            ],
        ];
        DB::table('investors')->upsert($investors, 'name', ['email', 'phone', 'address', 'nid_or_passport', 'total_invested', 'current_value', 'type', 'contact_person', 'contact_email', 'contact_phone', 'status', 'created_at']);
        $this->command->info('Investors seeded');
    }

    private function seedOrders(): void
    {
        $orders = [
            [
                'customer_name' => 'John Doe',
                'customer_email' => 'john@example.com',
                'customer_phone' => '+1234567890',
                'address_line' => '123 Main Street',
                'city' => 'Happyville',
                'postal_code' => '12345',
                'total_amount' => 5497,
                'status' => 'delivered',
                'payment_method' => 'cod',
                'created_at' => now()->subDays(5),
            ],
            [
                'customer_name' => 'Jane Smith',
                'customer_email' => 'jane@example.com',
                'customer_phone' => '+1987654321',
                'address_line' => '456 Oak Avenue',
                'city' => 'Sunnyvale',
                'postal_code' => '54321',
                'total_amount' => 3498,
                'status' => 'processing',
                'payment_method' => 'cod',
                'created_at' => now()->subDays(2),
            ],
            [
                'customer_name' => 'Bob Wilson',
                'customer_email' => 'bob@example.com',
                'customer_phone' => '+1122334455',
                'address_line' => '789 Pine Road',
                'city' => 'Cloudland',
                'postal_code' => '11111',
                'total_amount' => 7498,
                'status' => 'pending',
                'payment_method' => 'cod',
                'created_at' => now()->subHours(12),
            ],
            [
                'customer_name' => 'Alice Brown',
                'customer_email' => 'alice@example.com',
                'customer_phone' => '+1555666777',
                'address_line' => '321 Elm Street',
                'city' => 'Springfield',
                'postal_code' => '22222',
                'total_amount' => 8999,
                'status' => 'shipped',
                'payment_method' => 'online',
                'created_at' => now()->subDays(1),
            ],
        ];
        DB::table('orders')->upsert($orders, ['customer_email', 'created_at'], ['customer_name', 'customer_phone', 'address_line', 'city', 'postal_code', 'total_amount', 'status', 'payment_method', 'created_at']);

        // Seed order items
        $orderItems = [
            ['order_id' => 1, 'product_id' => 3, 'quantity' => 1, 'unit_price' => 2499, 'total_price' => 2499],
            ['order_id' => 1, 'product_id' => 6, 'quantity' => 2, 'unit_price' => 1499, 'total_price' => 2998],
            ['order_id' => 2, 'product_id' => 2, 'quantity' => 1, 'unit_price' => 1999, 'total_price' => 1999],
            ['order_id' => 2, 'product_id' => 5, 'quantity' => 1, 'unit_price' => 3499, 'total_price' => 3499],
            ['order_id' => 3, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 2999, 'total_price' => 2999],
            ['order_id' => 3, 'product_id' => 7, 'quantity' => 1, 'unit_price' => 3999, 'total_price' => 3999],
            ['order_id' => 4, 'product_id' => 10, 'quantity' => 1, 'unit_price' => 5999, 'total_price' => 5999],
            ['order_id' => 4, 'product_id' => 4, 'quantity' => 1, 'unit_price' => 1899, 'total_price' => 1899],
        ];
        DB::table('order_items')->insert($orderItems);
        $this->command->info('Orders seeded');
    }

    private function seedCoupons(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'description' => 'Welcome discount for new customers',
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 500,
                'max_discount' => 500,
                'usage_limit' => 100,
                'used_count' => 25,
                'valid_from' => now()->subDays(30),
                'valid_until' => now()->addDays(60),
                'status' => 'active',
                'created_at' => now()->subDays(30),
            ],
            [
                'code' => 'SAVE500',
                'description' => 'Flat BDT 500 off on orders above BDT 2000',
                'type' => 'fixed',
                'value' => 500,
                'min_order_amount' => 2000,
                'max_discount' => null,
                'usage_limit' => 50,
                'used_count' => 12,
                'valid_from' => now()->subDays(15),
                'valid_until' => now()->addDays(45),
                'status' => 'active',
                'created_at' => now()->subDays(15),
            ],
            [
                'code' => 'FLAT20',
                'description' => '20% off on all items',
                'type' => 'percentage',
                'value' => 20,
                'min_order_amount' => 1000,
                'max_discount' => 1000,
                'usage_limit' => null,
                'used_count' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addDays(30),
                'status' => 'active',
                'created_at' => now(),
            ],
        ];
        DB::table('coupons')->upsert($coupons, 'code', ['description', 'type', 'value', 'min_order_amount', 'max_discount', 'usage_limit', 'used_count', 'valid_from', 'valid_until', 'status', 'created_at']);
        $this->command->info('Coupons seeded');
    }

    private function seedFlashSales(): void
    {
        $flashSales = [
            [
                'name' => 'Weekend Flash Sale',
                'description' => 'Amazing discounts on selected items',
                'discount_percentage' => 25,
                'starts_at' => now()->subDays(2),
                'ends_at' => now()->addDays(2),
                'status' => 'active',
                'created_at' => now()->subDays(3),
            ],
        ];
        DB::table('flash_sales')->upsert($flashSales, 'name', ['description', 'discount_percentage', 'starts_at', 'ends_at', 'status', 'created_at']);
        $this->command->info('Flash sales seeded');
    }

    private function seedCmsPages(): void
    {
        $cmsPages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h1>About Kiddo\'s Heaven</h1><p>We are dedicated to bringing joy to children through high-quality toys and games.</p>',
                'meta_title' => 'About Us - Kiddo\'s Heaven',
                'meta_description' => 'Learn more about Kiddo\'s Heaven',
                'template' => 'default',
                'is_active' => true,
                'created_at' => now()->subDays(60),
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => '<h1>Contact Us</h1><p>Get in touch with us for any inquiries.</p>',
                'meta_title' => 'Contact Us - Kiddo\'s Heaven',
                'meta_description' => 'Contact Kiddo\'s Heaven',
                'template' => 'default',
                'is_active' => true,
                'created_at' => now()->subDays(55),
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'content' => '<h1>Terms & Conditions</h1><p>Please read our terms and conditions carefully.</p>',
                'meta_title' => 'Terms & Conditions - Kiddo\'s Heaven',
                'meta_description' => 'Terms and conditions',
                'template' => 'default',
                'is_active' => true,
                'created_at' => now()->subDays(50),
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us.</p>',
                'meta_title' => 'Privacy Policy - Kiddo\'s Heaven',
                'meta_description' => 'Privacy policy',
                'template' => 'default',
                'is_active' => true,
                'created_at' => now()->subDays(45),
            ],
        ];
        DB::table('cms_pages')->upsert($cmsPages, 'slug', ['title', 'content', 'meta_title', 'meta_description', 'template', 'is_active', 'created_at']);
        $this->command->info('CMS pages seeded');
    }

    private function seedReviews(): void
    {
        $reviews = [
            [
                'product_id' => 1,
                'user_id' => 2,
                'rating' => 5,
                'title' => 'Excellent toy!',
                'content' => 'My son loves this wooden train set. Great quality!',
                'is_approved' => true,
                'created_at' => now()->subDays(10),
            ],
            [
                'product_id' => 3,
                'user_id' => 3,
                'rating' => 4,
                'title' => 'Very soft and cuddly',
                'content' => 'The bear is very soft. Good value for money.',
                'is_approved' => true,
                'created_at' => now()->subDays(8),
            ],
            [
                'product_id' => 5,
                'user_id' => 4,
                'rating' => 5,
                'title' => 'Great educational toy',
                'content' => 'Helps my daughter learn math concepts. Highly recommended!',
                'is_approved' => true,
                'created_at' => now()->subDays(5),
            ],
        ];
        DB::table('reviews')->upsert($reviews, ['product_id', 'user_id', 'created_at'], ['rating', 'title', 'content', 'is_approved', 'created_at']);
        $this->command->info('Reviews seeded');
    }

    private function seedExpenseCategories(): void
    {
        $expenseCategories = [
            ['name' => 'Rent & Utilities', 'icon' => 'home', 'color' => '#6366f1', 'is_active' => true],
            ['name' => 'Salaries & Wages', 'icon' => 'users', 'color' => '#22c55e', 'is_active' => true],
            ['name' => 'Inventory & Supplies', 'icon' => 'shopping-cart', 'color' => '#f59e0b', 'is_active' => true],
            ['name' => 'Marketing & Advertising', 'icon' => 'megaphone', 'color' => '#ec4899', 'is_active' => true],
            ['name' => 'Shipping & Logistics', 'icon' => 'truck', 'color' => '#14b8a6', 'is_active' => true],
            ['name' => 'Software & Tools', 'icon' => 'computer', 'color' => '#8b5cf6', 'is_active' => true],
            ['name' => 'Maintenance & Repairs', 'icon' => 'wrench', 'color' => '#f97316', 'is_active' => true],
            ['name' => 'Insurance & Legal', 'icon' => 'shield', 'color' => '#0ea5e9', 'is_active' => true],
            ['name' => 'Miscellaneous', 'icon' => 'dots', 'color' => '#64748b', 'is_active' => true],
        ];
        DB::table('expense_categories')->upsert($expenseCategories, 'name', ['icon', 'color', 'is_active']);
        $this->command->info('Expense categories seeded');
    }

    private function seedExpenses(): void
    {
        $expenses = [
            [
                'title' => 'Warehouse Rent - February',
                'description' => 'Monthly rent for main warehouse',
                'amount' => 25000,
                'expense_date' => now()->subDays(5),
                'category_id' => 1,
                'status' => 'approved',
                'created_at' => now()->subDays(5),
            ],
            [
                'title' => 'Staff Salaries - February',
                'description' => 'Monthly salaries for warehouse and admin staff',
                'amount' => 85000,
                'expense_date' => now()->subDays(3),
                'category_id' => 2,
                'status' => 'approved',
                'created_at' => now()->subDays(3),
            ],
            [
                'title' => 'Product Inventory Purchase',
                'description' => 'Bulk purchase of new toy inventory',
                'amount' => 150000,
                'expense_date' => now()->subDays(10),
                'category_id' => 3,
                'status' => 'approved',
                'created_at' => now()->subDays(10),
            ],
            [
                'title' => 'Facebook Ads Campaign',
                'description' => 'Targeted advertising for new product launch',
                'amount' => 15000,
                'expense_date' => now()->subDays(7),
                'category_id' => 4,
                'status' => 'approved',
                'created_at' => now()->subDays(7),
            ],
            [
                'title' => 'Courier Services',
                'description' => 'Delivery charges for pending orders',
                'amount' => 5500,
                'expense_date' => now()->subDays(2),
                'category_id' => 5,
                'status' => 'pending',
                'created_at' => now()->subDays(2),
            ],
        ];
        DB::table('expenses')->upsert($expenses, 'title', ['description', 'amount', 'expense_date', 'category_id', 'status', 'created_at']);
        $this->command->info('Expenses seeded');
    }

    private function seedLoyaltyPrograms(): void
    {
        $loyaltyPrograms = [
            [
                'name' => 'Bronze Member',
                'description' => 'Entry level membership',
                'points_per_currency' => 1,
                'minimum_points' => 100,
                'discount_percentage' => 0,
                'is_active' => true,
                'created_at' => now()->subDays(60),
            ],
            [
                'name' => 'Silver Member',
                'description' => 'Mid-level membership with discounts',
                'points_per_currency' => 1.5,
                'minimum_points' => 500,
                'discount_percentage' => 2,
                'is_active' => true,
                'created_at' => now()->subDays(55),
            ],
            [
                'name' => 'Gold Member',
                'description' => 'Premium membership with better benefits',
                'points_per_currency' => 2,
                'minimum_points' => 1500,
                'discount_percentage' => 5,
                'is_active' => true,
                'created_at' => now()->subDays(50),
            ],
            [
                'name' => 'Platinum Member',
                'description' => 'Top tier membership with best benefits',
                'points_per_currency' => 3,
                'minimum_points' => 5000,
                'discount_percentage' => 10,
                'is_active' => true,
                'created_at' => now()->subDays(45),
            ],
        ];
        DB::table('loyalty_programs')->upsert($loyaltyPrograms, 'name', ['description', 'points_per_currency', 'minimum_points', 'discount_percentage', 'is_active', 'created_at']);
        $this->command->info('Loyalty programs seeded');
    }

    private function seedPurchaseBatches(): void
    {
        $products = DB::table('products')->pluck('id')->toArray();
        $suppliers = ['Bangla Suppliers', 'Dhaka Traders', 'Export House', 'Local Manufacturer', 'International Imports'];
        $batchPrefixes = ['PB-2024-', 'PB-2025-', 'IMP-', 'LOC-'];

        $batches = [];
        for ($i = 1; $i <= 25; $i++) {
            $productId = $products[array_rand($products)];
            $quantity = rand(20, 200);
            $unitCost = rand(50, 5000) / 10;

            $batches[] = [
                'batch_number' => $batchPrefixes[array_rand($batchPrefixes)] . str_pad($i, 4, '0', STR_PAD_LEFT),
                'product_id' => $productId,
                'unit_cost' => $unitCost,
                'quantity_received' => $quantity,
                'remaining_quantity' => $quantity - rand(0, (int)($quantity * 0.7)),
                'quantity_reserved' => 0,
                'supplier' => $suppliers[array_rand($suppliers)],
                'supplier_invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'purchase_date' => now()->subDays(rand(1, 180))->format('Y-m-d'),
                'manufacture_date' => rand(0, 1) ? now()->subDays(rand(10, 60))->format('Y-m-d') : null,
                'expiry_date' => rand(0, 1) ? now()->addDays(rand(30, 365))->format('Y-m-d') : null,
                'notes' => 'Batch created for testing - ' . $i,
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now(),
            ];
        }
        DB::table('purchase_batches')->insert($batches);
        $this->command->info('Purchase batches seeded');
    }

    private function seedInventoryMovements(): void
    {
        $products = DB::table('products')->pluck('id')->toArray();
        $batches = DB::table('purchase_batches')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();
        $movementTypes = ['purchase', 'sale', 'adjustment', 'return', 'transfer'];

        $movements = [];
        for ($i = 1; $i <= 35; $i++) {
            $productId = $products[array_rand($products)];
            $type = $movementTypes[array_rand($movementTypes)];
            $quantity = rand(1, 50);
            $unitCost = rand(50, 5000) / 10;

            $movements[] = [
                'movement_number' => 'MOV-' . strtoupper(Str::random(10)),
                'product_id' => $productId,
                'batch_id' => !empty($batches) && $type != 'purchase' ? $batches[array_rand($batches)] : null,
                'movement_type' => $type,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'reference_type' => rand(0, 1) ? 'order' : null,
                'reference_id' => rand(0, 1) ? rand(1, 100) : null,
                'user_id' => $users[array_rand($users)] ?? null,
                'notes' => 'Test movement ' . $i . ' - ' . $type,
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now(),
            ];
        }
        DB::table('inventory_movements')->insert($movements);
        $this->command->info('Inventory movements seeded');
    }

    private function seedCapitalAccounts(): void
    {
        $users = DB::table('users')->where('is_admin', false)->pluck('id')->toArray();
        $accountTypes = ['partner', 'investor'];
        $types = ['capital_contribution', 'purchase_contribution', 'profit_share', 'withdrawal'];
        $accounts = [];

        foreach ($users as $index => $userId) {
            $type = $accountTypes[$index % count($accountTypes)];
            $balance = rand(50000, 500000);

            $accounts[] = [
                'account_number' => Str::uuid()->toString(),
                'owner_type' => $type,
                'owner_id' => $userId,
                'type' => $types[0],
                'name' => ucfirst($type) . ' Capital Account - User ' . $userId,
                'balance' => $balance,
                'total_credited' => $balance,
                'total_debited' => 0,
                'profit_share_percentage' => rand(10, 30),
                'expense_share_percentage' => rand(5, 15),
                'status' => 'active',
                'created_at' => now()->subDays(rand(30, 365)),
                'updated_at' => now(),
            ];
        }

        DB::table('capital_accounts')->insert($accounts);
        $this->command->info('Capital accounts seeded');
    }

    private function seedFinancialTransactions(): void
    {
        $accounts = DB::table('capital_accounts')->pluck('id')->toArray();
        $transactionTypes = ['credit', 'debit'];
        $referenceTypes = ['investment', 'withdrawal', 'profit_distribution', 'fee', 'adjustment'];

        $transactions = [];
        for ($i = 1; $i <= 30; $i++) {
            $accountId = $accounts[array_rand($accounts)];
            $type = $transactionTypes[array_rand($transactionTypes)];
            $amount = rand(5000, 50000);

            $creditDescriptions = [
                'Initial capital investment',
                'Additional capital contribution',
                'Profit distribution received',
                'Loan received',
                'Capital injection',
                'Partner contribution',
            ];

            $debitDescriptions = [
                'Withdrawal for personal use',
                'Business expense',
                'Profit withdrawal',
                'Fee payment',
                'Capital withdrawal',
                'Investment return',
            ];

            $transactions[] = [
                'capital_account_id' => $accountId,
                'transaction_type' => $type,
                'amount' => $amount,
                'transaction_date' => now()->subDays(rand(1, 180))->format('Y-m-d'),
                'description' => $type == 'credit' ? $creditDescriptions[array_rand($creditDescriptions)] : $debitDescriptions[array_rand($debitDescriptions)],
                'reference_type' => $referenceTypes[array_rand($referenceTypes)],
                'reference_id' => rand(1, 100),
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now(),
            ];
        }
        DB::table('financial_transactions')->insert($transactions);
        $this->command->info('Financial transactions seeded');
    }

    private function seedPartnerPayments(): void
    {
        $partners = DB::table('partners')->pluck('id')->toArray();

        $payments = [
            [
                'partner_id' => $partners[0] ?? 1,
                'amount' => 5000,
                'payment_date' => now()->subDays(15),
                'reference' => 'BT-2024-001',
                'status' => 'completed',
                'notes' => 'Monthly commission payment',
                'created_at' => now()->subDays(15),
            ],
            [
                'partner_id' => $partners[1] ?? 2,
                'amount' => 3500,
                'payment_date' => now()->subDays(10),
                'reference' => 'BT-2024-002',
                'status' => 'completed',
                'notes' => 'Q1 commission payment',
                'created_at' => now()->subDays(10),
            ],
            [
                'partner_id' => $partners[3] ?? 4,
                'amount' => 7500,
                'payment_date' => now()->subDays(5),
                'reference' => 'BK-2024-001',
                'status' => 'completed',
                'notes' => 'Affiliate commission for January',
                'created_at' => now()->subDays(5),
            ],
        ];
        DB::table('partner_payments')->insert($payments);
        $this->command->info('Partner payments seeded');
    }

    private function seedPartnerCalculations(): void
    {
        $partners = DB::table('partners')->pluck('id')->toArray();

        $calculations = [
            [
                'partner_id' => $partners[0] ?? 1,
                'total_sales' => 125000,
                'commission_amount' => 10625,
                'payment_amount' => 10625,
                'period_start' => now()->subMonths(1)->startOfMonth()->format('Y-m-d'),
                'period_end' => now()->subMonths(1)->endOfMonth()->format('Y-m-d'),
                'status' => 'approved',
                'created_at' => now()->subDays(3),
            ],
            [
                'partner_id' => $partners[3] ?? 4,
                'total_sales' => 45000,
                'commission_amount' => 6750,
                'payment_amount' => 6750,
                'period_start' => now()->subMonths(1)->startOfMonth()->format('Y-m-d'),
                'period_end' => now()->subMonths(1)->endOfMonth()->format('Y-m-d'),
                'status' => 'pending',
                'created_at' => now()->subDays(1),
            ],
        ];
        DB::table('partner_calculations')->insert($calculations);
        $this->command->info('Partner calculations seeded');
    }

    private function seedInvestments(): void
    {
        $investors = DB::table('investors')->pluck('id')->toArray();
        $investments = [];

        foreach ($investors as $index => $investorId) {
            $investments[] = [
                'investor_id' => $investorId,
                'amount' => rand(50000, 200000),
                'investment_date' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                'investment_type' => ['equity', 'debt', 'convertible'][array_rand(['equity', 'debt', 'convertible'])],
                'terms' => 'Standard investment terms',
                'status' => 'active',
                'created_at' => now()->subMonths(rand(1, 12)),
            ];
        }
        DB::table('investments')->insert($investments);
        $this->command->info('Investments seeded');
    }
}
