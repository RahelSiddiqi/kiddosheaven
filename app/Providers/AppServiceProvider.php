<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.simple-default');

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
