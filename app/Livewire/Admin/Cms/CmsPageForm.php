<?php

namespace App\Livewire\Admin\Cms;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Illuminate\Support\Str;
use App\Models\CmsPage;

#[Layout('admin.layouts.app')]
#[Title('CMS Page - Admin')]
class CmsPageForm extends Component
{
    #[Locked]
    public ?int $pageId = null;

    // Form fields
    public string $title           = '';
    public string $slug            = '';
    public string $content         = '';
    public string $metaTitle       = '';
    public string $metaDescription = '';
    public bool   $isActive        = true;
    public bool   $showInFooter    = false;
    public bool   $showInHeader    = false;
    public int    $sortOrder       = 0;

    public function mount(?int $pageId = null): void
    {
        $this->pageId = $pageId;

        if ($pageId) {
            $page = CmsPage::findOrFail($pageId);

            $this->title           = $page->title;
            $this->slug            = $page->slug;
            $this->content         = $page->content ?? '';
            $this->metaTitle       = $page->meta_title ?? '';
            $this->metaDescription = $page->meta_description ?? '';
            $this->isActive        = (bool) $page->is_active;
            $this->showInFooter    = (bool) $page->show_in_footer;
            $this->showInHeader    = (bool) $page->show_in_header;
            $this->sortOrder       = (int) ($page->sort_order ?? 0);
        }
    }

    public function updatedTitle(): void
    {
        if (!$this->pageId) {
            $this->slug = Str::slug($this->title);
        }
    }

    protected function rules(): array
    {
        $uniqueSlug = $this->pageId
            ? 'unique:cms_pages,slug,' . $this->pageId
            : 'unique:cms_pages,slug';

        return [
            'title'           => 'required|string|max:255',
            'slug'            => "required|string|max:255|{$uniqueSlug}",
            'content'         => 'nullable|string',
            'metaTitle'       => 'nullable|string|max:255',
            'metaDescription' => 'nullable|string',
            'isActive'        => 'boolean',
            'showInFooter'    => 'boolean',
            'showInHeader'    => 'boolean',
            'sortOrder'       => 'integer|min:0',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title'            => $this->title,
            'slug'             => $this->slug ?: Str::slug($this->title),
            'content'          => $this->content,
            'meta_title'       => $this->metaTitle ?: null,
            'meta_description' => $this->metaDescription ?: null,
            'is_active'        => $this->isActive,
            'show_in_footer'   => $this->showInFooter,
            'show_in_header'   => $this->showInHeader,
            'sort_order'       => $this->sortOrder,
        ];

        if ($this->pageId) {
            CmsPage::findOrFail($this->pageId)->update($data);
            $this->dispatch('notify', message: 'Page updated.', type: 'success');
        } else {
            CmsPage::create($data);
            $this->dispatch('notify', message: 'Page created.', type: 'success');
        }

        $this->redirect(route('admin.cms.pages.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.cms.cms-page-form');
    }
}
