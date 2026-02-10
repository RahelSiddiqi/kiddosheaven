<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repository Contracts
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\CatalogRepositoryInterface;

// Repository Implementations
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\CatalogRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(CatalogRepositoryInterface::class, CatalogRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
