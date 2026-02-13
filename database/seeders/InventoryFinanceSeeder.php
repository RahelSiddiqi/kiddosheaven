<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InventoryFinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 20+ records for each new table for testing.
     */
    public function run(): void
    {
        // Ensure we have products to work with
        $products = DB::table('products')->count();
        if ($products == 0) {
            $this->createProducts();
        }

        $products = DB::table('products')->pluck('id')->toArray();
        $partners = DB::table('partners')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();

        if (empty($partners)) {
            $partners = $this->createPartners();
        } else {
            $partners = DB::table('partners')->pluck('id')->toArray();
        }

        if (empty($users)) {
            $users = $this->createUsers();
        } else {
            $users = DB::table('users')->where('is_admin', false)->pluck('id')->toArray();
        }

        // Create Purchase Batches (20+ records)
        $this->createPurchaseBatches($products);

        // Create Inventory Movements (30+ records)
        $this->createInventoryMovements($products, $users);

        // Create Capital Accounts (10+ records)
        $this->createCapitalAccounts($users);

        // Create Financial Transactions (25+ records)
        $this->createFinancialTransactions($users);

        $this->command->info('Inventory and Finance seeders completed successfully!');
    }

    private function createProducts(): void
    {
        $products = [
            ['name' => 'Organic Baby Food', 'sku' => 'BF-001', 'price' => 250, 'cost_price' => 150, 'stock' => 100, 'category_id' => 1],
            ['name' => 'Baby Diapers Pack', 'sku' => 'DP-001', 'price' => 800, 'cost_price' => 500, 'stock' => 200, 'category_id' => 1],
            ['name' => 'Baby Wipes', 'sku' => 'BW-001', 'price' => 150, 'cost_price' => 80, 'stock' => 300, 'category_id' => 1],
            ['name' => 'Baby Shampoo', 'sku' => 'BS-001', 'price' => 350, 'cost_price' => 180, 'stock' => 150, 'category_id' => 1],
            ['name' => 'Baby Lotion', 'sku' => 'BL-001', 'price' => 400, 'cost_price' => 220, 'stock' => 120, 'category_id' => 1],
            ['name' => 'Soft Teddy Bear', 'sku' => 'TB-001', 'price' => 800, 'cost_price' => 350, 'stock' => 50, 'category_id' => 2],
            ['name' => 'Building Blocks Set', 'sku' => 'BB-001', 'price' => 1200, 'cost_price' => 500, 'stock' => 75, 'category_id' => 2],
            ['name' => 'Educational Puzzle', 'sku' => 'EP-001', 'price' => 600, 'cost_price' => 250, 'stock' => 100, 'category_id' => 2],
            ['name' => 'Remote Control Car', 'sku' => 'RC-001', 'price' => 1500, 'cost_price' => 700, 'stock' => 40, 'category_id' => 2],
            ['name' => 'Doll House', 'sku' => 'DH-001', 'price' => 2000, 'cost_price' => 900, 'stock' => 30, 'category_id' => 2],
            ['name' => 'Baby Onesie Set', 'sku' => 'OS-001', 'price' => 500, 'cost_price' => 200, 'stock' => 200, 'category_id' => 3],
            ['name' => 'Baby Socks Pack', 'sku' => 'SK-001', 'price' => 200, 'cost_price' => 80, 'stock' => 300, 'category_id' => 3],
            ['name' => 'Baby Hat', 'sku' => 'HT-001', 'price' => 250, 'cost_price' => 100, 'stock' => 150, 'category_id' => 3],
            ['name' => 'Baby Blanket', 'sku' => 'BLK-001', 'price' => 600, 'cost_price' => 280, 'stock' => 80, 'category_id' => 3],
            ['name' => 'Baby Booties', 'sku' => 'BT-001', 'price' => 350, 'cost_price' => 150, 'stock' => 120, 'category_id' => 3],
            ['name' => 'Baby Bottle', 'sku' => 'BBT-001', 'price' => 450, 'cost_price' => 200, 'stock' => 180, 'category_id' => 1],
            ['name' => 'Pacifier Set', 'sku' => 'PC-001', 'price' => 300, 'cost_price' => 120, 'stock' => 200, 'category_id' => 1],
            ['name' => 'Baby Nail Clipper', 'sku' => 'NC-001', 'price' => 180, 'cost_price' => 70, 'stock' => 150, 'category_id' => 1],
            ['name' => 'Baby Thermometer', 'sku' => 'TH-001', 'price' => 500, 'cost_price' => 220, 'stock' => 100, 'category_id' => 1],
            ['name' => 'Baby Stroller', 'sku' => 'ST-001', 'price' => 8500, 'cost_price' => 4000, 'stock' => 20, 'category_id' => 1],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'sku' => $product['sku'],
                'description' => 'Sample product description for ' . $product['name'],
                'price' => $product['price'],
                'cost_price' => $product['cost_price'],
                'stock' => $product['stock'],
                'category_id' => $product['category_id'] ?? 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Created ' . count($products) . ' products');
    }

    private function createPartners(): array
    {
        $partners = [
            ['name' => 'Ahmed Trading Co.', 'email' => 'ahmed@trading.com', 'phone' => '+8801712345678', 'commission_rate' => 15],
            ['name' => 'Baba Enterprise', 'email' => 'baba@enterprise.com', 'phone' => '+8801712345679', 'commission_rate' => 12],
            ['name' => 'Baby World Ltd.', 'email' => 'baby@world.com', 'phone' => '+8801712345680', 'commission_rate' => 18],
            ['name' => 'Kids Paradise', 'email' => 'kids@paradise.com', 'phone' => '+8801712345681', 'commission_rate' => 10],
            ['name' => 'Mother Care', 'email' => 'mother@care.com', 'phone' => '+8801712345682', 'commission_rate' => 14],
        ];

        foreach ($partners as $partner) {
            DB::table('partners')->insert([
                'name' => $partner['name'],
                'email' => $partner['email'],
                'phone' => $partner['phone'],
                'commission_rate' => $partner['commission_rate'],
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Created ' . count($partners) . ' partners');
        return DB::table('partners')->pluck('id')->toArray();
    }

    private function createUsers(): array
    {
        $users = [
            ['name' => 'Rahim Khan', 'email' => 'rahim@example.com', 'password' => Hash::make('password123')],
            ['name' => 'Karim Mia', 'email' => 'karim@example.com', 'password' => Hash::make('password123')],
            ['name' => 'Fatima Begum', 'email' => 'fatima@example.com', 'password' => Hash::make('password123')],
            ['name' => 'Saleha Khatun', 'email' => 'saleha@example.com', 'password' => Hash::make('password123')],
            ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => Hash::make('password123')],
        ];

        foreach ($users as $index => $user) {
            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'is_admin' => false,
                'is_active' => true,
                'created_at' => now()->subDays($index * 30),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Created ' . count($users) . ' users');
        return DB::table('users')->where('is_admin', false)->pluck('id')->toArray();
    }

    private function createPurchaseBatches(array $productIds): void
    {
        $suppliers = ['Bangla Suppliers', 'Dhaka Traders', 'Export House', 'Local Manufacturer', 'International Imports'];
        $batchPrefixes = ['PB-2024-', 'PB-2025-', 'IMP-', 'LOC-'];

        $batches = [];

        // Create 25 purchase batches
        for ($i = 1; $i <= 25; $i++) {
            $productId = $productIds[array_rand($productIds)];
            $quantity = rand(20, 200);
            $unitCost = rand(50, 5000) / 10; // 5 to 500 BDT

            $batches[] = [
                'batch_number' => $batchPrefixes[array_rand($batchPrefixes)] . str_pad($i, 4, '0', STR_PAD_LEFT),
                'product_id' => $productId,
                'unit_cost' => $unitCost,
                'quantity_received' => $quantity,
                'remaining_quantity' => $quantity - rand(0, $quantity * 0.7), // Some sold, some remaining
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

        foreach ($batches as $batch) {
            DB::table('purchase_batches')->insert($batch);
        }

        $this->command->info('Created ' . count($batches) . ' purchase batches');
    }

    private function createInventoryMovements(array $productIds, array $userIds): void
    {
        $movementTypes = ['purchase', 'sale', 'adjustment', 'return', 'transfer'];
        $batches = DB::table('purchase_batches')->pluck('id')->toArray();

        $movements = [];

        // Create 35 inventory movements
        for ($i = 1; $i <= 35; $i++) {
            $productId = $productIds[array_rand($productIds)];
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
                'user_id' => $userIds[array_rand($userIds)] ?? null,
                'notes' => 'Test movement ' . $i . ' - ' . $type,
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now(),
            ];
        }

        foreach ($movements as $movement) {
            DB::table('inventory_movements')->insert($movement);
        }

        $this->command->info('Created ' . count($movements) . ' inventory movements');
    }

    private function createCapitalAccounts(array $userIds): void
    {
        $accountTypes = ['partner', 'investor'];

        $accounts = [];

        // Create 12 capital accounts
        for ($i = 1; $i <= 12; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $type = $accountTypes[array_rand($accountTypes)];
            $openingBalance = rand(50000, 500000);
            $currentBalance = $openingBalance + rand(-50000, 100000);

            // Check if account already exists for this user
            $exists = DB::table('capital_accounts')
                ->where('partner_id', $userId)
                ->where('account_type', $type)
                ->exists();

            if (!$exists) {
                $accounts[] = [
                    'partner_id' => $userId,
                    'account_type' => $type,
                    'opening_balance' => $openingBalance,
                    'current_balance' => $currentBalance,
                    'notes' => 'Capital account for ' . ($type == 'partner' ? 'partner' : 'investor') . ' - ' . $i,
                    'created_at' => now()->subDays(rand(30, 365)),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($accounts as $account) {
            DB::table('capital_accounts')->insert($account);
        }

        $this->command->info('Created ' . count($accounts) . ' capital accounts');
    }

    private function createFinancialTransactions(array $userIds): void
    {
        $transactionTypes = ['credit', 'debit'];
        $accounts = DB::table('capital_accounts')->pluck('id')->toArray();
        $referenceTypes = ['investment', 'withdrawal', 'profit_distribution', 'fee', 'adjustment'];

        if (empty($accounts)) {
            // Create accounts first
            foreach ($userIds as $index => $userId) {
                DB::table('capital_accounts')->insert([
                    'partner_id' => $userId,
                    'account_type' => $index < count($userIds) / 2 ? 'partner' : 'investor',
                    'opening_balance' => rand(50000, 200000),
                    'current_balance' => rand(50000, 250000),
                    'notes' => 'Auto-created account for user ' . $userId,
                    'created_at' => now()->subDays(rand(30, 180)),
                    'updated_at' => now(),
                ]);
            }
            $accounts = DB::table('capital_accounts')->pluck('id')->toArray();
        }

        $transactions = [];

        // Create 30 financial transactions
        for ($i = 1; $i <= 30; $i++) {
            $accountId = $accounts[array_rand($accounts)];
            $type = $transactionTypes[array_rand($transactionTypes)];
            $amount = rand(5000, 50000);

            $transactions[] = [
                'capital_account_id' => $accountId,
                'transaction_type' => $type,
                'amount' => $amount,
                'transaction_date' => now()->subDays(rand(1, 180))->format('Y-m-d'),
                'description' => $this->getTransactionDescription($type),
                'reference_type' => $referenceTypes[array_rand($referenceTypes)],
                'reference_id' => rand(1, 100),
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now(),
            ];
        }

        foreach ($transactions as $transaction) {
            DB::table('financial_transactions')->insert($transaction);
        }

        $this->command->info('Created ' . count($transactions) . ' financial transactions');
    }

    private function getTransactionDescription(string $type): string
    {
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

        return $type == 'credit'
            ? $creditDescriptions[array_rand($creditDescriptions)]
            : $debitDescriptions[array_rand($debitDescriptions)];
    }
}
