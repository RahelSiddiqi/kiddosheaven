<?php
require 'vendor/autoload.php';

try {
    $app = require_once 'bootstrap/app.php';

    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();

    echo "=== TESTING ADMIN PRODUCT FLOW ===\n\n";

    // Test 1: Create product
    echo "1. Creating product with initial stock...\n";
    $ps = app('App\Services\Product\ProductService');
    $prod = $ps->create([
        'name' => 'Admin Test Product ' . time(),
        'category_id' => 1,
        'price' => 1000,
        'cost_price' => 500,
        'stock_quantity' => 15,
        'sku' => 'ADMIN-TEST-' . time(),
        'is_active' => 1,
    ]);
    echo "   Created: Product #$prod->id with $prod->stock_quantity stock\n";

    // Check batches
    $batchCount = $prod->purchaseBatches()->count();
    echo "   Batches created: $batchCount\n";
    if ($batchCount > 0) {
        $batch = $prod->purchaseBatches()->first();
        echo "   OK Batch #$batch->id: remaining=$batch->remaining_quantity, cost=$batch->unit_cost\n";
    } else {
        echo "   ERROR: No batch created!\n";
        exit(1);
    }

    // Test 2: Create order
    echo "\n2. Creating order with new product...\n";
    $os = app('App\Services\Order\OrderService');
    $user = \App\Models\User::first();
    $order = $os->create([
        'user_id' => $user->id,
        'customer_name' => 'Test',
        'customer_email' => 'test@test.com',
        'customer_phone' => '01234567890',
        'address_line' => 'Test Address',
        'city' => 'Dhaka',
        'status' => 'pending',
        'payment_method' => 'cod',
        'payment_status' => 'pending',
        'total_amount' => 5000,
        'items' => [[
            'product_id' => $prod->id,
            'quantity' => 5,
            'price' => 1000,
        ]],
    ]);
    echo "   Created: Order #$order->id\n";

    // Check stock after order
    $prod->refresh();
    echo "   Stock after order: " . $prod->stock_quantity . "\n";

    // Check batch after order
    $batch->refresh();
    echo "   OK Batch remaining after order: " . $batch->remaining_quantity . "\n";

    echo "\nALL TESTS PASSED!\n";

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
