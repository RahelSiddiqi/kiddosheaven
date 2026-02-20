<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Themes\ThemeRegistry;
use App\Themes\ThemeRenderer;
use App\Themes\DefaultTheme\DefaultTheme;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeRegistry::class, function () {
            return new ThemeRegistry();
        });

        $this->app->singleton(ThemeRenderer::class, function ($app) {
            return new ThemeRenderer($app->make(ThemeRegistry::class));
        });
    }

    public function boot(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $registry->register('default', new DefaultTheme());

        // Share theme colors and typography with the storefront layout
        View::composer('layouts.livewire', function ($view) use ($registry) {
            $theme = $registry->getDefault();
            $colors = $theme->getDefaultColors();
            $typography = $theme->getDefaultTypography();

            // Try to load overrides from DB (SiteThemeSetting)
            try {
                $siteId = app()->bound('current.site') ? app('current.site')->id : null;
                $settings = $siteId
                    ? \App\Domains\Site\Models\SiteThemeSetting::where('site_id', $siteId)->first()
                    : \App\Domains\Site\Models\SiteThemeSetting::first();
                if ($settings) {
                    $colors = array_merge($colors, $settings->colors ?? []);
                    $typography = array_merge($typography, $settings->typography ?? []);
                }
            } catch (\Exception $e) {
                // Table might not exist yet
            }

            $view->with('themeColors', $colors);
            $view->with('themeTypography', $typography);
        });
    }
}
