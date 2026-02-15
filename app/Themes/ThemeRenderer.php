<?php

namespace App\Themes;

use App\Themes\Contracts\ThemeInterface;
use App\Domains\Site\Models\SiteThemeSetting;
use Illuminate\Support\Facades\View;

class ThemeRenderer
{
    public function __construct(
        protected ThemeRegistry $registry
    ) {}

    /**
     * Render a page with the active theme.
     */
    public function renderPage(string $page, array $data = []): string
    {
        $theme = $this->getActiveTheme();
        $settings = $this->getThemeSettings();

        // Inject theme settings into data
        $data = array_merge($data, [
            'theme' => $theme,
            'themeSettings' => $settings,
            'colors' => $settings->colors ?? $theme->getDefaultColors(),
            'typography' => $settings->typography ?? $theme->getDefaultTypography(),
        ]);

        return View::make($page, $data)->render();
    }

    /**
     * Render a section component.
     */
    public function renderSection(string $section, array $data = []): string
    {
        $theme = $this->getActiveTheme();
        return $theme->renderSection($section, $data);
    }

    /**
     * Render homepage sections based on layout config.
     */
    public function renderHomepage(array $additionalData = []): string
    {
        $settings = $this->getThemeSettings();
        $layoutConfig = $settings->layout_config ?? $this->getDefaultLayoutConfig();

        $sections = [];
        foreach ($layoutConfig['homepage_sections'] ?? [] as $sectionConfig) {
            if (!$sectionConfig['enabled']) {
                continue;
            }

            $sections[] = $this->renderSection(
                $sectionConfig['component'],
                array_merge($additionalData, $sectionConfig['props'] ?? [])
            );
        }

        return implode("\n", $sections);
    }

    /**
     * Get the active theme instance.
     */
    protected function getActiveTheme(): ThemeInterface
    {
        $settings = $this->getThemeSettings();
        $themeName = $settings->active_theme ?? 'default';

        return $this->registry->get($themeName) ?? $this->registry->getDefault();
    }

    /**
     * Get theme settings for current site.
     */
    protected function getThemeSettings(): ?SiteThemeSetting
    {
        // For now, return the first site's settings
        // In multi-site setup, this would resolve based on current domain
        return SiteThemeSetting::first();
    }

    /**
     * Get default layout configuration.
     */
    protected function getDefaultLayoutConfig(): array
    {
        return [
            'homepage_sections' => [
                ['component' => 'hero', 'enabled' => true, 'order' => 1, 'props' => []],
                ['component' => 'featured-products', 'enabled' => true, 'order' => 2, 'props' => ['limit' => 8]],
                ['component' => 'categories', 'enabled' => true, 'order' => 3, 'props' => []],
                ['component' => 'flash-sale', 'enabled' => true, 'order' => 4, 'props' => []],
            ],
        ];
    }

    /**
     * Get theme assets (CSS/JS) for current theme.
     */
    public function getAssets(): array
    {
        $theme = $this->getActiveTheme();
        return $theme->getAssets();
    }
}
