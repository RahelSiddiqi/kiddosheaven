<?php

namespace App\Livewire\Admin\Shipping;

use App\Domains\Shipping\Models\ShippingZone;
use App\Domains\Shipping\Services\ShippingService;
use Livewire\Component;
use Livewire\WithPagination;

class ShippingZoneIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $editingId = null;

    // Form fields
    public string $name = '';
    public array $countries = [];
    public bool $is_rest_of_world = false;
    public bool $is_active = true;

    public function openCreate(): void
    {
        $this->reset(['name', 'countries', 'is_rest_of_world', 'is_active', 'editingId']);
        $this->is_active = true;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $zone = ShippingZone::findOrFail($id);
        $this->editingId       = $zone->id;
        $this->name            = $zone->name;
        $this->countries       = $zone->countries ?? [];
        $this->is_rest_of_world = $zone->is_rest_of_world;
        $this->is_active       = $zone->is_active;
        $this->showForm        = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = [
            'name'              => $this->name,
            'countries'         => $this->countries,
            'is_rest_of_world'  => $this->is_rest_of_world,
            'is_active'         => $this->is_active,
        ];

        if ($this->editingId) {
            ShippingZone::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Shipping zone updated.');
        } else {
            ShippingZone::create($data);
            session()->flash('success', 'Shipping zone created.');
        }

        $this->showForm = false;
        $this->reset(['name', 'countries', 'is_rest_of_world', 'editingId']);
        $this->is_active = true;
    }

    public function delete(int $id): void
    {
        ShippingZone::findOrFail($id)->delete();
        session()->flash('success', 'Shipping zone deleted.');
    }

    public function render()
    {
        $zones = ShippingZone::with('rates')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->paginate(20);

        return view('livewire.admin.shipping.zone-index', compact('zones'))
            ->layout('layouts.admin');
    }
}
