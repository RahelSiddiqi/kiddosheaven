<?php

namespace App\Themes\DefaultTheme;

use App\Themes\Contracts\ThemeInterface;
use Illuminate\Support\Facades\View;

class DefaultTheme implements ThemeInterface
{
    public function getName(): string
    {
        return 'Default Theme';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getConfig(): array
    {
        return [
            'name' => $this->getName(),
            'version' => $this->getVersion(),
            'author' => 'Kiddo\'s Heaven',
            'description' => 'Default responsive theme for Kiddo\'s Heaven',
            'supports' => [
                'hero_section' => true,
                'product_grid' => true,
                'cart' => true,
                'wishlist' => true,
                'reviews' => true,
            ],
        ];
    }

    public function getAvailableSections(): array
    {
        return [
            'hero' => 'Hero Section',
            'featured-products' => 'Featured Products Grid',
            'categories' => 'Category Showcase',
            'flash-sale' => 'Flash Sale Banner',
            'newsletter' => 'Newsletter Signup',
            'testimonials' => 'Customer Testimonials',
        ];
    }

    public function getDefaultColors(): array
    {
        return [
            'primary' => '#018790',
            'primary-dark' => '#005461',
            'accent' => '#00b7b5',
            'secondary' => '#f59e0b',
            'neutral' => '#6b7280',
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'error' => '#ef4444',
            'background' => '#f9fafb',
            'surface' => '#ffffff',
            'text' => '#1f2937',
        ];
    }

    public function getDefaultTypography(): array
    {
        return [
            'font_family_base' => 'Nunito, system-ui, sans-serif',
            'font_family_heading' => 'Nunito, system-ui, sans-serif',
            'font_size_base' => '16px',
            'font_size_lg' => '18px',
            'font_size_xl' => '20px',
            'line_height_base' => '1.5',
        ];
    }

    public function renderSection(string $section, array $data = []): string
    {
        $viewPath = "themes.default.sections.{$section}";

        if (!View::exists($viewPath)) {
            return "<!-- Section {$section} not found -->";
        }

        return View::make($viewPath, $data)->render();
    }

    public function getAssets(): array
    {
        return [
            'css' => [
                '/themes/default/css/theme.css',
            ],
            'js' => [
                '/themes/default/js/theme.js',
            ],
        ];
    }
}
