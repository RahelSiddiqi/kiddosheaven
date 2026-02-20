<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Legacy services
        $this->app->singleton(\App\Services\CurrencyService::class);
        $this->app->singleton(\App\Services\TaxService::class);

        // Domain services
        $this->app->singleton(\App\Domains\Payment\Services\PaymentService::class);
        $this->app->singleton(\App\Domains\GiftCard\Services\GiftCardService::class);
        $this->app->singleton(\App\Domains\Tax\Services\ZoneTaxService::class);
        $this->app->singleton(\App\Domains\B2B\Services\B2BPricingService::class);
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.simple-default');

        \Illuminate\Support\Facades\Blade::directive('price', function ($expression) {
            return "<?php echo app(\App\Services\CurrencyService::class)->format($expression); ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('tax', function ($expression) {
            return "<?php echo app(\App\Services\TaxService::class)->calculate($expression); ?>";
        });

        // Register webhook event listeners
        Event::listen(
            \App\Events\OrderPlaced::class,
            \App\Listeners\DispatchOrderPlacedWebhook::class,
        );

        Event::listen(
            \App\Events\OrderPlaced::class,
            \App\Listeners\SendOrderConfirmationEmail::class,
        );

        Event::listen(
            \App\Events\ShipmentShipped::class,
            \App\Listeners\SendOrderShippedEmail::class,
        );

        Event::listen(
            \App\Events\ShipmentDelivered::class,
            \App\Listeners\SendOrderDeliveredEmail::class,
        );

        // Share all site settings with every view (cached for 60 min)
        View::composer('*', function ($view) {
            $settings = Cache::remember('site_settings', 3600, function () {
                try {
                    return Setting::pluck('value', 'key')->toArray();
                } catch (\Throwable $e) {
                    return [];
                }
            });
            $view->with('siteSettings', $settings);
        });
    }
}
