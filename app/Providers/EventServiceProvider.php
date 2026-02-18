<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Listeners\SendOrderConfirmation;
use App\Listeners\UpdateOrderStatusHistory;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            SendOrderConfirmation::class,
            // Stock deduction is now handled by InventoryService->deductStock()
            // during order creation, not via event listener
        ],
        OrderStatusChanged::class => [
            UpdateOrderStatusHistory::class,
        ],
        // LowStockAlert::class => [
        //     SendLowStockNotification::class,
        // ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
