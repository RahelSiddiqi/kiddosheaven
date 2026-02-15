<?php

namespace App\Themes;

use App\Themes\Contracts\ThemeInterface;

class ThemeRegistry
{
    protected array $themes = [];

    public function register(string $name, ThemeInterface $theme): void
    {
        $this->themes[$name] = $theme;
    }

    public function get(string $name): ?ThemeInterface
    {
        return $this->themes[$name] ?? null;
    }

    public function all(): array
    {
        return $this->themes;
    }

    public function has(string $name): bool
    {
        return isset($this->themes[$name]);
    }

    public function getDefault(): ThemeInterface
    {
        return $this->get('default') ?? throw new \Exception('Default theme not registered');
    }
}
