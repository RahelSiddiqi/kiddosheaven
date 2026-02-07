<?php

namespace App\Listeners;

use App\Events\LowStockAlert;
use Illuminate\Support\Facades\Log;

class SendLowStockNotification
{
    public function handle(LowStockAlert $event): void
    {
        $product = $event->product;
        $currentStock = $event->currentStock;

        Log::warning("Low stock alert: {$product->name}", [
            'product_id' => $product->id,
            'current_stock' => $currentStock,
            'threshold' => $product->low_stock_threshold,
        ]);
    }
}
