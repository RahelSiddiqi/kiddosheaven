<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Repositories\ProductRepository;

class DeductInventory
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;

        foreach ($order->items as $item) {
            $this->productRepository->deductStock($item->product_id, $item->quantity);
        }
    }
}
