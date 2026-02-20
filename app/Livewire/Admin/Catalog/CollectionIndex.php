<?php

namespace App\Livewire\Admin\Catalog;

use App\Domains\Catalog\Models\Collection;
use App\Domains\Catalog\Services\CollectionService;
use Livewire\Component;
use Livewire\WithPagination;

class CollectionIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function delete(int $id): void
    {
        Collection::findOrFail($id)->delete();
        session()->flash('success', 'Collection deleted.');
    }

    public function toggleActive(int $id): void
    {
        $c = Collection::findOrFail($id);
        $c->update(['is_active' => !$c->is_active]);
    }

    public function syncAutomatic(int $id, CollectionService $service): void
    {
        $collection = Collection::findOrFail($id);
        if ($collection->isAutomatic()) {
            $service->syncAutomaticProducts($collection);
            session()->flash('success', 'Automatic collection synced.');
        }
    }

    public function render()
    {
        $collections = Collection::withCount('products')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('position')
            ->paginate(20);

        return view('livewire.admin.catalog.collection-index', compact('collections'))
            ->layout('layouts.admin');
    }
}
