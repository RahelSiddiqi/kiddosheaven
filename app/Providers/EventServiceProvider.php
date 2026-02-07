<?php

namespace App\Providers;

use App\Events\LowStockAlert;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Listeners\DeductInventory;
use App\Listeners\SendLowStockNotification;
use App\Listeners\SendOrderConfirmation;
use App\Listeners\UpdateOrderStatusHistory;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            SendOrderConfirmation::class,
            DeductInventory::class,
        ],
        OrderStatusChanged::class => [
            UpdateOrderStatusHistory::class,
        ],
        LowStockAlert::class => [
            SendLowStockNotification::class,
        ],
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
