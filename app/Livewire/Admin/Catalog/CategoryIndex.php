<?php

namespace App\Livewire\Admin\Catalog;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

#[Layout('admin.layouts.app')]
#[Title('Categories - Admin')]
class CategoryIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    // Modal state
    public bool $showModal = false;
    public ?int $editingId = null;

    // Form fields
    public string $name        = '';
    public string $slug        = '';
    public ?int   $parentId    = null;
    public string $description = '';
    public bool   $isActive    = true;
    public bool   $showOnHome  = false;
    public int    $sortOrder   = 0;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedName(): void
    {
        if (!$this->editingId) {
            $this->slug = Str::slug($this->name);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function categoryClass(): string
    {
        return class_exists(\App\Domains\Catalog\Models\Category::class)
            ? \App\Domains\Catalog\Models\Category::class
            : \App\Models\Category::class;
    }

    // ── Modal control ─────────────────────────────────────────────

    public function createModal(): void
    {
        $this->reset(['name', 'slug', 'parentId', 'description', 'sortOrder']);
        $this->isActive   = true;
        $this->showOnHome = false;
        $this->editingId  = null;
        $this->showModal  = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $modelClass = $this->categoryClass();
        $cat = $modelClass::findOrFail($id);

        $this->editingId   = $id;
        $this->name        = $cat->name;
        $this->slug        = $cat->slug ?? Str::slug($cat->name);
        $this->parentId    = $cat->parent_id;
        $this->description = $cat->description ?? '';
        $this->isActive    = (bool) $cat->is_active;
        $this->showOnHome  = (bool) ($cat->show_on_home ?? false);
        $this->sortOrder   = (int)  ($cat->sort_order ?? 0);
        $this->showModal   = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    // ── CRUD ─────────────────────────────────────────────────────

    public function save(): void
    {
        $modelClass = $this->categoryClass();

        $rules = [
            'name'      => 'required|string|max:150',
            'isActive'  => 'boolean',
            'sortOrder' => 'integer|min:0',
        ];

        if ($this->editingId) {
            $rules['slug'] = 'required|string|max:150|unique:categories,slug,' . $this->editingId;
        } else {
            $rules['slug'] = 'required|string|max:150|unique:categories,slug';
        }

        $this->validate($rules);

        $data = [
            'name'         => $this->name,
            'slug'         => $this->slug ?: Str::slug($this->name),
            'parent_id'    => $this->parentId,
            'description'  => $this->description ?: null,
            'is_active'    => $this->isActive,
            'show_on_home' => $this->showOnHome,
            'sort_order'   => $this->sortOrder,
        ];

        if ($this->editingId) {
            $modelClass::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'Category updated.', type: 'success');
        } else {
            $modelClass::create($data);
            $this->dispatch('notify', message: 'Category created.', type: 'success');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function deleteRecord(int $id): void
    {
        $modelClass = $this->categoryClass();
        $cat = $modelClass::withCount('products')->find($id);

        if (!$cat) {
            return;
        }

        if (($cat->products_count ?? 0) > 0) {
            $this->dispatch('notify', message: 'Cannot delete: category has products.', type: 'error');
            return;
        }

        $cat->delete();
        $this->dispatch('notify', message: 'Category deleted.', type: 'success');
    }

    public function render()
    {
        $modelClass = $this->categoryClass();

        $query = method_exists($modelClass, 'withoutGlobalScope')
            ? $modelClass::withoutGlobalScope('site')
            : $modelClass::query();

        $categories = $query->with('parent')
            ->withCount('products')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('parent_id')
            ->orderBy('name')
            ->paginate(20);

        $parents = $query->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->when($this->editingId, fn($c) => $c->reject(fn($p) => $p->id === $this->editingId));

        return view('livewire.admin.catalog.category-index', compact('categories', 'parents'));
    }
}
