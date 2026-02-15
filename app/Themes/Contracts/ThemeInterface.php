<?php

namespace App\Themes\Contracts;

interface ThemeInterface
{
    /**
     * Get the theme name.
     */
    public function getName(): string;

    /**
     * Get the theme version.
     */
    public function getVersion(): string;

    /**
     * Get the theme configuration.
     */
    public function getConfig(): array;

    /**
     * Get available sections for this theme.
     */
    public function getAvailableSections(): array;

    /**
     * Get the default theme colors.
     */
    public function getDefaultColors(): array;

    /**
     * Get the default typography settings.
     */
    public function getDefaultTypography(): array;

    /**
     * Render a section with given data.
     */
    public function renderSection(string $section, array $data = []): string;

    /**
     * Get theme assets (CSS, JS).
     */
    public function getAssets(): array;
}
