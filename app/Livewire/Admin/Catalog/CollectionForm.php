<?php

namespace App\Livewire\Admin\Catalog;

use App\Domains\Catalog\Models\Collection;
use App\Domains\Catalog\Services\CollectionService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CollectionForm extends Component
{
    use WithFileUploads;

    public ?int $collectionId = null;

    public string $name          = '';
    public string $slug          = '';
    public string $description   = '';
    public string $type          = 'manual';
    public string $sort_order    = 'manual';
    public bool   $is_active     = true;
    public bool   $is_featured   = false;
    public string $meta_title    = '';
    public string $meta_description = '';

    // Automatic collection conditions
    public array $conditions     = [];
    public string $condition_match = 'all';

    protected $rules = [
        'name'             => 'required|string|max:255',
        'slug'             => 'required|string|max:255',
        'description'      => 'nullable|string',
        'type'             => 'required|in:manual,automatic',
        'sort_order'       => 'required|string',
        'is_active'        => 'boolean',
        'is_featured'      => 'boolean',
        'meta_title'       => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
        'condition_match'  => 'in:all,any',
    ];

    public function mount(int $id = null): void
    {
        if ($id) {
            $this->collectionId = $id;
            $collection = Collection::findOrFail($id);

            $this->name              = $collection->name;
            $this->slug              = $collection->slug;
            $this->description       = $collection->description ?? '';
            $this->type              = $collection->type;
            $this->sort_order        = $collection->sort_order;
            $this->is_active         = $collection->is_active;
            $this->is_featured       = $collection->is_featured;
            $this->meta_title        = $collection->meta_title ?? '';
            $this->meta_description  = $collection->meta_description ?? '';
            $this->conditions        = $collection->conditions ?? [];
            $this->condition_match   = $collection->condition_match ?? 'all';
        }
    }

    public function updatedName(): void
    {
        if (!$this->collectionId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function addCondition(): void
    {
        $this->conditions[] = ['field' => 'title', 'operator' => 'contains', 'value' => ''];
    }

    public function removeCondition(int $index): void
    {
        unset($this->conditions[$index]);
        $this->conditions = array_values($this->conditions);
    }

    public function save(CollectionService $service): mixed
    {
        $this->validate();

        $data = [
            'name'              => $this->name,
            'slug'              => $this->slug,
            'description'       => $this->description ?: null,
            'type'              => $this->type,
            'sort_order'        => $this->sort_order,
            'is_active'         => $this->is_active,
            'is_featured'       => $this->is_featured,
            'meta_title'        => $this->meta_title ?: null,
            'meta_description'  => $this->meta_description ?: null,
            'conditions'        => $this->type === 'automatic' ? $this->conditions : null,
            'condition_match'   => $this->condition_match,
        ];

        if ($this->collectionId) {
            $service->update(Collection::findOrFail($this->collectionId), $data);
            session()->flash('success', 'Collection updated.');
        } else {
            $service->create($data);
            session()->flash('success', 'Collection created.');
        }

        return redirect()->route('admin.collections.index');
    }

    public function render()
    {
        return view('livewire.admin.catalog.collection-form')
            ->layout('layouts.admin');
    }
}
