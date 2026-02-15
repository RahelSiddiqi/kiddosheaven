<?php

namespace App\Domains\Site\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteThemeSetting extends Model
{
    protected $fillable = [
        'site_id',
        'active_theme',
        'colors',
        'typography',
        'layout_config',
        'custom_css',
        'header_config',
        'footer_config',
    ];

    protected $casts = [
        'colors' => 'array',
        'typography' => 'array',
        'layout_config' => 'array',
        'header_config' => 'array',
        'footer_config' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the homepage sections config.
     */
    public function getHomepageSections(): array
    {
        return $this->layout_config['homepage'] ?? [];
    }

    /**
     * Get enabled homepage sections only.
     */
    public function getEnabledHomepageSections(): array
    {
        return collect($this->getHomepageSections())
            ->filter(fn($section) => $section['enabled'] ?? true)
            ->values()
            ->toArray();
    }

    /**
     * Export to theme array for rendering.
     */
    public function toThemeArray(): array
    {
        return [
            'theme' => $this->active_theme ?? 'default',
            'colors' => $this->colors ?? [],
            'typography' => $this->typography ?? [],
            'layout' => $this->layout_config ?? [],
            'header' => $this->header_config ?? [],
            'footer' => $this->footer_config ?? [],
            'custom_css' => $this->custom_css ?? '',
        ];
    }
}
