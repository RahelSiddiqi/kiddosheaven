<?php

namespace App\Livewire\Admin\Theme;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin.layouts.app')]
#[Title('Theme Settings - Admin')]
class ThemeSettings extends Component
{
    public string $primaryColor = '#3b82f6';
    public string $secondaryColor = '#6b7280';
    public string $accentColor = '#f59e0b';
    public string $fontFamily = 'Inter';
    public string $activeTheme = 'default';

    protected $settingId = null;

    public array $themes = [
        'default' => 'Default',
        'minimal' => 'Minimal',
        'bold' => 'Bold',
    ];

    public array $fonts = [
        'Inter' => 'Inter',
        'Poppins' => 'Poppins',
        'Roboto' => 'Roboto',
        'Nunito' => 'Nunito',
    ];

    public function mount(): void
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        try {
            // Try Domains model first
            try {
                $model = \App\Domains\Site\Models\SiteThemeSetting::class;
            } catch (\Exception $e) {
                $model = \App\Models\SiteThemeSetting::class;
            }

            $siteId = app('current.site')?->id ?? 1;
            $setting = $model::where('site_id', $siteId)->first();

            if ($setting) {
                $this->settingId = $setting->id;
                $this->activeTheme = $setting->active_theme ?? 'default';

                $colors = $setting->colors ?? [];
                $this->primaryColor = $colors['primary'] ?? '#3b82f6';
                $this->secondaryColor = $colors['secondary'] ?? '#6b7280';
                $this->accentColor = $colors['accent'] ?? '#f59e0b';

                $typography = $setting->typography ?? [];
                $this->fontFamily = $typography['font_family'] ?? 'Inter';
            }
        } catch (\Exception $e) {
            // Use defaults if model or site not available
        }
    }

    public function save(): void
    {
        try {
            // Try Domains model first
            try {
                $model = \App\Domains\Site\Models\SiteThemeSetting::class;
            } catch (\Exception $e) {
                $model = \App\Models\SiteThemeSetting::class;
            }

            $siteId = app('current.site')?->id ?? 1;

            $model::updateOrCreate(
                ['site_id' => $siteId],
                [
                    'active_theme' => $this->activeTheme,
                    'colors' => [
                        'primary' => $this->primaryColor,
                        'secondary' => $this->secondaryColor,
                        'accent' => $this->accentColor,
                    ],
                    'typography' => [
                        'font_family' => $this->fontFamily,
                    ],
                ]
            );

            $this->dispatch('theme-saved');
            $this->dispatch('notify', message: 'Theme settings saved!');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error saving theme settings: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.theme.theme-settings');
    }
}
