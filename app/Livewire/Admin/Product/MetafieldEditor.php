<?php

namespace App\Livewire\Admin\Product;

use App\Domains\Metafield\Models\Metafield;
use Livewire\Component;

class MetafieldEditor extends Component
{
    /** @var string product|order|customer|page */
    public string $ownerType = 'product';
    public int $ownerId;

    // Form fields
    public bool   $showForm    = false;
    public ?int   $editingId   = null;
    public string $namespace   = 'custom';
    public string $key         = '';
    public string $value       = '';
    public string $value_type  = 'string';

    protected array $rules = [
        'namespace'  => 'required|max:64|alpha_dash',
        'key'        => 'required|max:64|alpha_dash',
        'value'      => 'required',
        'value_type' => 'required|in:string,integer,json,boolean',
    ];

    public function getMetasProperty()
    {
        return Metafield::where('owner_type', $this->ownerType)
            ->where('owner_id', $this->ownerId)
            ->orderBy('namespace')
            ->orderBy('key')
            ->get();
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'namespace', 'key', 'value', 'value_type']);
        $this->namespace  = 'custom';
        $this->value_type = 'string';
        $this->showForm   = true;
    }

    public function openEdit(int $id): void
    {
        $meta = Metafield::findOrFail($id);
        $this->editingId  = $id;
        $this->namespace  = $meta->namespace;
        $this->key        = $meta->key;
        $this->value      = $meta->value;
        $this->value_type = $meta->value_type;
        $this->showForm   = true;
    }

    public function setMeta(): void
    {
        $this->validate();

        $data = [
            'owner_type' => $this->ownerType,
            'owner_id'   => $this->ownerId,
            'namespace'  => $this->namespace,
            'key'        => $this->key,
            'value'      => $this->value,
            'value_type' => $this->value_type,
        ];

        if ($this->editingId) {
            Metafield::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', type: 'success', message: 'Metafield updated.');
        } else {
            Metafield::updateOrCreate(
                ['owner_type' => $this->ownerType, 'owner_id' => $this->ownerId, 'namespace' => $this->namespace, 'key' => $this->key],
                $data
            );
            $this->dispatch('toast', type: 'success', message: 'Metafield saved.');
        }

        $this->showForm = false;
        $this->reset(['editingId', 'namespace', 'key', 'value', 'value_type']);
    }

    public function deleteMeta(int $id): void
    {
        Metafield::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Metafield deleted.');
    }

    public function render()
    {
        return view('livewire.admin.product.metafield-editor', [
            'metas' => $this->metas,
        ]);
    }
}
