<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\View\View;

#[Layout('layouts.livewire')]
class CmsPageShow extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        // Try domain model first, fall back to app model
        $modelClass = class_exists(\App\Domains\Content\Models\CmsPage::class)
            ? \App\Domains\Content\Models\CmsPage::class
            : \App\Models\CmsPage::class;

        $page = $modelClass::where('slug', $this->slug)
            ->where('is_active', true)
            ->firstOrFail();

        view()->share('page_title', $page->title);
        view()->share('page_description', $page->meta_description ?? '');

        return view('livewire.storefront.cms-page-show', compact('page'));
    }
}
