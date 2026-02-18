<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clean up test data
\Illuminate\Support\Facades\DB::table('orders')->where('id', '>=', 5)->delete();
\Illuminate\Support\Facades\DB::table('purchase_batches')->where('product_id', 2)->delete();
\Illuminate\Support\Facades\DB::table('inventory_movements')->where('product_id', 2)->delete();
\App\Models\Product::find(2)->forceFill(['stock_quantity' => 10])->save();

$product = \App\Models\Product::find(2);
echo "Initial stock: " . $product->stock_quantity . "\n";

$orderService = app('App\Services\Order\OrderService');
$user = \App\Models\User::first();

$order = $orderService->create([
    'user_id'        => $user->id,
    'customer_name'  => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '01234567890',
    'address_line'   => '123 Main St',
    'city'           => 'Dhaka',
    'status'         => 'pending',
    'payment_method' => 'cod',
    'payment_status' => 'pending',
    'total_amount'   => 1300,
    'items'          => [
        [
            'product_id'   => 2,
            'quantity'     => 2,
            'price'        => 650,
        ]
    ]
]);

$product->refresh();
echo "Stock after order: " . $product->stock_quantity . " (expected: 8)\n";
echo "Order #" . $order->id . " created successfully!\n";
echo "\nMovements:\n";
$movements = \App\Models\InventoryMovement::where('product_id', 2)->get();
foreach ($movements as $m) {
    echo "  qty=" . $m->quantity . ", type=" . $m->movement_type . "\n";
}

echo "\n✅ TEST PASSED - Order can be placed!\n";
