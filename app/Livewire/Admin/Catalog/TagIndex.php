<?php

namespace App\Livewire\Admin\Catalog;

use App\Domains\Catalog\Models\Tag;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Tags')]
class TagIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search   = '';
    public string $typeFilter = '';

    public bool   $showForm  = false;
    public ?int   $editingId = null;
    public string $name      = '';
    public string $type      = 'product';
    public string $color     = '#6366f1';

    protected array $rules = [
        'name'  => 'required|string|max:100',
        'type'  => 'required|in:product,order,customer,collection,general',
        'color' => 'nullable|string|max:20',
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedTypeFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'color']);
        $this->type  = 'product';
        $this->color = '#6366f1';
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $tag->name;
        $this->type      = $tag->type;
        $this->color     = $tag->color ?? '#6366f1';
        $this->showForm  = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'  => $this->name,
            'type'  => $this->type,
            'color' => $this->color,
        ];

        if ($this->editingId) {
            Tag::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', type: 'success', message: 'Tag updated.');
        } else {
            Tag::create($data);
            $this->dispatch('toast', type: 'success', message: 'Tag created.');
        }

        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Tag::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Tag deleted.');
    }

    public function render()
    {
        $tags = Tag::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->withCount(['products', 'orders'])
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(25);

        return view('livewire.admin.catalog.tag-index', compact('tags'));
    }
}
