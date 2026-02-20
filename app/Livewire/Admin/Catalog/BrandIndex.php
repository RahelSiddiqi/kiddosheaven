<?php

namespace App\Livewire\Admin\Catalog;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

#[Layout('admin.layouts.app')]
#[Title('Brands - Admin')]
class BrandIndex extends Component
{
    use WithPagination, WithFileUploads;

    #[Url(as: 'q')]
    public string $search = '';

    // Modal state
    public bool $showModal = false;
    public ?int $editingId = null;

    // Form fields
    public string  $name        = '';
    public string  $slug        = '';
    public string  $description = '';
    public string  $website     = '';
    public bool    $isActive    = true;
    public ?string $existingLogo = null;
    public $logo  = null; // uploaded file

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

    private function brandClass(): string
    {
        return class_exists(\App\Domains\Catalog\Models\Brand::class)
            ? \App\Domains\Catalog\Models\Brand::class
            : \App\Models\Brand::class;
    }

    // ── Modal control ─────────────────────────────────────────────

    public function createModal(): void
    {
        $this->reset(['name', 'slug', 'description', 'website', 'logo', 'existingLogo']);
        $this->isActive  = true;
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $modelClass = $this->brandClass();
        $brand = $modelClass::findOrFail($id);

        $this->editingId    = $id;
        $this->name         = $brand->name;
        $this->slug         = $brand->slug ?? Str::slug($brand->name);
        $this->description  = $brand->description ?? '';
        $this->website      = $brand->website ?? '';
        $this->isActive     = (bool) $brand->is_active;
        $this->existingLogo = $brand->logo;
        $this->logo         = null;
        $this->showModal    = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->logo      = null;
    }

    // ── CRUD ─────────────────────────────────────────────────────

    public function save(): void
    {
        $modelClass = $this->brandClass();

        $rules = [
            'name'     => 'required|string|max:150',
            'isActive' => 'boolean',
            'logo'     => 'nullable|image|max:2048',
        ];

        if ($this->editingId) {
            $rules['slug'] = 'required|string|max:150|unique:brands,slug,' . $this->editingId;
        } else {
            $rules['slug'] = 'required|string|max:150|unique:brands,slug';
        }

        $this->validate($rules);

        $logoPath = $this->existingLogo;
        if ($this->logo) {
            if ($this->existingLogo) {
                Storage::disk('public')->delete($this->existingLogo);
            }
            $logoPath = $this->logo->store('brands', 'public');
        }

        $data = [
            'name'        => $this->name,
            'slug'        => $this->slug ?: Str::slug($this->name),
            'description' => $this->description ?: null,
            'website'     => $this->website ?: null,
            'is_active'   => $this->isActive,
            'logo'        => $logoPath,
        ];

        if ($this->editingId) {
            $modelClass::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'Brand updated.', type: 'success');
        } else {
            $modelClass::create($data);
            $this->dispatch('notify', message: 'Brand created.', type: 'success');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $modelClass = $this->brandClass();
        $brand = $modelClass::findOrFail($id);
        $brand->update(['is_active' => !$brand->is_active]);
        $this->dispatch('notify', message: 'Brand status toggled.', type: 'success');
    }

    public function deleteRecord(int $id): void
    {
        $modelClass = $this->brandClass();
        $brand = $modelClass::withCount('products')->findOrFail($id);

        if (($brand->products_count ?? 0) > 0) {
            $this->dispatch('notify', message: 'Cannot delete: brand has products.', type: 'error');
            return;
        }

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();
        $this->dispatch('notify', message: 'Brand deleted.', type: 'success');
    }

    public function render()
    {
        $modelClass = $this->brandClass();

        $query = method_exists($modelClass, 'withoutGlobalScope')
            ? $modelClass::withoutGlobalScope('site')
            : $modelClass::query();

        $brands = $query
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount(['products' => function ($q) {
                if (method_exists($q->getModel(), 'withoutGlobalScope')) {
                    $q->withoutGlobalScope('site');
                }
            }])
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.catalog.brand-index', compact('brands'));
    }
}
