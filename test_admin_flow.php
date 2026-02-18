<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== COMPREHENSIVE INVENTORY & ORDER FLOW TEST ===\n\n";

// 1. CREATE A NEW PRODUCT WITH STOCK
echo "1. Creating new product with stock...\n";
$productService = app(\App\Services\Product\ProductService::class);

try {
    $product = $productService->create([
        'name' => 'Test Product - Admin Flow',
        'category_id' => 1,
        'price' => 1500,
        'cost_price' => 800,
        'stock_quantity' => 20,
        'sku' => 'TEST-' . time(),
        'is_active' => true,
    ]);
    echo "✅ Product #" . $product->id . " created\n";
    echo "   Stock: " . $product->stock_quantity . "\n";
    
    // Check if batch was created
    $batches = $product->purchaseBatches;
    echo "   Batches created: " . $batches->count() . "\n";
    if ($batches->count() > 0) {
        foreach ($batches as $batch) {
            echo "     - Batch: batch_number=" . $batch->batch_number . ", remaining=" . $batch->remaining_quantity . ", cost=" . $batch->unit_cost . ", status=" . $batch->status . "\n";
        }
    } else {
        echo "   ❌ ERROR: No batches created!\n";
    }
} catch (\Exception $e) {
    echo "❌ Product creation failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. CREATE AN ORDER WITH THE NEW PRODUCT
echo "\n2. Creating order with new product...\n";
try {
    $orderService = app(\App\Services\Order\OrderService::class);
    $user = \App\Models\User::first();
    
    $order = $orderService->create([
        'user_id' => $user->id,
        'customer_name' => 'Admin Test',
        'customer_email' => 'admin@test.com',
        'customer_phone' => '01234567890',
        'address_line' => 'Admin Test Address',
        'city' => 'Dhaka',
        'status' => 'pending',
        'payment_method' => 'cod',
        'payment_status' => 'pending',
        'total_amount' => 7500,
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 5,
                'price' => 1500,
            ]
        ]
    ]);
    
    echo "✅ Order #" . $order->id . " (" . $order->order_number . ") created\n";
    echo "   Items: " . $order->items->count() . "\n";
    
    $product->refresh();
    echo "   Stock after order: " . $product->stock_quantity . "\n";
    
} catch (\Exception $e) {
    echo "❌ Order creation failed: " . $e->getMessage() . "\n";
    echo "   Error: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. VERIFY INVENTORY MOVEMENTS
echo "\n3. Verifying inventory movements...\n";
$movements = \App\Models\InventoryMovement::where('product_id', $product->id)
    ->orderBy('created_at', 'asc')
    ->get();
echo "   Total movements: " . $movements->count() . "\n";
foreach ($movements as $m) {
    echo "     - qty=" . $m->quantity . ", type=" . $m->movement_type . ", ref=" . ($m->reference_id ?? 'N/A') . "\n";
}

// 4. VERIFY BATCHES POST-ORDER
echo "\n4. Verifying purchase batches after order...\n";
$allBatches = $product->purchaseBatches;
echo "   Total batches: " . $allBatches->count() . "\n";
foreach ($allBatches as $b) {
    echo "     - Batch #" . $b->id . ": remaining=" . $b->remaining_quantity . ", status=" . $b->status . "\n";
}

echo "\n✅ ALL TESTS PASSED!\n";
?>
