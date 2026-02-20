<?php

namespace App\Livewire\Admin\Cms;

use App\Models\CmsPage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('CMS Pages - Admin')]
class CmsPageIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Computed]
    public function pages()
    {
        return CmsPage::when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%");
            }))
            ->when($this->status === 'published', fn($q) => $q->where('is_active', true))
            ->when($this->status === 'draft', fn($q) => $q->where('is_active', false))
            ->orderByDesc('updated_at')
            ->paginate(20);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => CmsPage::count(),
            'published' => CmsPage::where('is_active', true)->count(),
            'drafts' => CmsPage::where('is_active', false)->count(),
        ];
    }

    public function togglePublish(int $id): void
    {
        $page = CmsPage::findOrFail($id);
        $page->update(['is_active' => !$page->is_active]);
        unset($this->pages, $this->stats);
        $this->dispatch('notify', message: 'Page status updated.', type: 'success');
    }

    public function delete(int $id): void
    {
        CmsPage::findOrFail($id)->delete();
        unset($this->pages, $this->stats);
        $this->dispatch('notify', message: 'Page deleted.', type: 'success');
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function render()
    {
        return view('livewire.admin.cms.cms-page-index');
    }
}
