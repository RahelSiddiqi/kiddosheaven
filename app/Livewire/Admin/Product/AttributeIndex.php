<?php

namespace App\Livewire\Admin\Product;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

#[Layout('admin.layouts.app')]
#[Title('Attributes - Admin')]
class AttributeIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    // Modal state
    public bool  $showModal  = false;
    public ?int  $editingId  = null;

    // Form fields
    public string $name           = '';
    public string $slug           = '';
    public string $type           = 'select';
    public string $description    = '';
    public bool   $isRequired     = false;
    public bool   $isFilterable   = true;
    public bool   $useForVariants = false;
    public int    $sortOrder      = 0;

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

    private function attributeClass(): string
    {
        return class_exists(\App\Domains\Product\Models\ProductAttribute::class)
            ? \App\Domains\Product\Models\ProductAttribute::class
            : \App\Models\ProductAttribute::class;
    }

    // ── Modal control ─────────────────────────────────────────────

    public function createModal(): void
    {
        $this->reset(['name', 'slug', 'description', 'isRequired', 'sortOrder']);
        $this->type           = 'select';
        $this->isFilterable   = true;
        $this->useForVariants = false;
        $this->editingId      = null;
        $this->showModal      = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $attrClass = $this->attributeClass();
        $attr = $attrClass::findOrFail($id);

        $this->editingId      = $id;
        $this->name           = $attr->name;
        $this->slug           = $attr->slug ?? '';
        $this->type           = $attr->type;
        $this->description    = $attr->description ?? '';
        $this->isRequired     = (bool) $attr->is_required;
        $this->isFilterable   = (bool) $attr->is_filterable;
        $this->useForVariants = (bool) $attr->use_for_variants;
        $this->sortOrder      = (int) ($attr->sort_order ?? 0);
        $this->showModal      = true;
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
        $attrClass = $this->attributeClass();

        $slugRule = $this->editingId
            ? 'nullable|string|max:100|unique:product_attributes,slug,' . $this->editingId
            : 'nullable|string|max:100|unique:product_attributes,slug';

        $this->validate([
            'name'      => 'required|string|max:255',
            'slug'      => $slugRule,
            'type'      => 'required|in:text,number,select,multiselect,boolean,date,color',
            'sortOrder' => 'integer|min:0',
        ]);

        $data = [
            'name'             => $this->name,
            'slug'             => $this->slug ?: Str::slug($this->name),
            'type'             => $this->type,
            'description'      => $this->description ?: null,
            'is_required'      => $this->isRequired,
            'is_filterable'    => $this->isFilterable,
            'use_for_variants' => $this->useForVariants,
            'sort_order'       => $this->sortOrder,
        ];

        if ($this->editingId) {
            $attrClass::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'Attribute updated.', type: 'success');
        } else {
            $attrClass::create($data);
            $this->dispatch('notify', message: 'Attribute created.', type: 'success');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function deleteRecord(int $id): void
    {
        $attrClass = $this->attributeClass();
        $attr = $attrClass::withCount('values')->findOrFail($id);

        $attr->delete();
        $this->dispatch('notify', message: 'Attribute deleted.', type: 'success');
        $this->resetPage();
    }

    public function render()
    {
        $attrClass = $this->attributeClass();

        $attributes = $attrClass::withCount('values')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        $stats = [
            'total'        => $attrClass::count(),
            'for_variants' => $attrClass::where('use_for_variants', true)->count(),
            'filterable'   => $attrClass::where('is_filterable', true)->count(),
        ];

        return view('livewire.admin.product.attribute-index', compact('attributes', 'stats'));
    }
}
