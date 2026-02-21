<?php

namespace App\Livewire\Admin\Settings;

use App\Domains\Tax\Models\TaxRate;
use App\Domains\Tax\Models\TaxZone;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin.layouts.app')]
#[Title('Tax Zones')]
class TaxZoneIndex extends Component
{
    public bool   $showZoneForm = false;
    public bool   $showRateForm = false;
    public ?int   $editingZoneId = null;
    public ?int   $editingRateId = null;
    public ?int   $activeZoneId = null;  // zone we're adding rates to

    // Zone form
    public string $zone_name       = '';
    public string $zone_countries  = '';  // comma-separated ISO codes
    public bool   $zone_is_default = false;
    public bool   $zone_is_active  = true;

    // Rate form
    public string $rate_name        = '';
    public float  $rate_rate        = 0;
    public string $rate_tax_class   = 'standard';
    public bool   $rate_is_compound = false;
    public bool   $rate_is_active   = true;
    public string $rate_applies_to  = 'all';

    protected array $rules = [
        'zone_name'       => 'required|string|max:100',
        'rate_name'       => 'required|string|max:100',
        'rate_rate'       => 'required|numeric|min:0|max:100',
        'rate_tax_class'  => 'required|in:standard,reduced,zero,exempt',
        'rate_applies_to' => 'required|in:all,physical,digital,shipping',
    ];

    // ── Zone ──────────────────────────────────────────────────────

    public function openCreateZone(): void
    {
        $this->reset(['editingZoneId', 'zone_name', 'zone_countries', 'zone_is_default']);
        $this->zone_is_active = true;
        $this->showZoneForm   = true;
    }

    public function openEditZone(int $id): void
    {
        $z = TaxZone::findOrFail($id);
        $this->editingZoneId   = $id;
        $this->zone_name       = $z->name;
        $this->zone_countries  = implode(', ', $z->countries ?? []);
        $this->zone_is_default = (bool) $z->is_default;
        $this->zone_is_active  = (bool) $z->is_active;
        $this->showZoneForm    = true;
    }

    public function saveZone(): void
    {
        $this->validate(['zone_name' => 'required|string|max:100']);

        $countries = array_filter(array_map('trim', explode(',', $this->zone_countries)));

        if ($this->zone_is_default) {
            TaxZone::where('is_default', true)->update(['is_default' => false]);
        }

        $data = [
            'name'       => $this->zone_name,
            'countries'  => array_values($countries),
            'is_default' => $this->zone_is_default,
            'is_active'  => $this->zone_is_active,
        ];

        if ($this->editingZoneId) {
            TaxZone::findOrFail($this->editingZoneId)->update($data);
            $this->dispatch('toast', type: 'success', message: 'Tax zone updated.');
        } else {
            TaxZone::create($data);
            $this->dispatch('toast', type: 'success', message: 'Tax zone created.');
        }

        $this->showZoneForm = false;
    }

    public function deleteZone(int $id): void
    {
        TaxZone::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Tax zone deleted.');
    }

    // ── Rates ─────────────────────────────────────────────────────

    public function openAddRate(int $zoneId): void
    {
        $this->activeZoneId   = $zoneId;
        $this->editingRateId  = null;
        $this->rate_name      = '';
        $this->rate_rate      = 0;
        $this->rate_tax_class = 'standard';
        $this->rate_is_compound = false;
        $this->rate_is_active = true;
        $this->rate_applies_to = 'all';
        $this->showRateForm   = true;
    }

    public function openEditRate(int $id): void
    {
        $r = TaxRate::findOrFail($id);
        $this->editingRateId    = $id;
        $this->activeZoneId     = $r->tax_zone_id;
        $this->rate_name        = $r->name;
        $this->rate_rate        = (float) $r->rate;
        $this->rate_tax_class   = $r->tax_class;
        $this->rate_is_compound = (bool) $r->is_compound;
        $this->rate_is_active   = (bool) $r->is_active;
        $this->rate_applies_to  = $r->applies_to;
        $this->showRateForm     = true;
    }

    public function saveRate(): void
    {
        $this->validate([
            'rate_name'       => 'required|string|max:100',
            'rate_rate'       => 'required|numeric|min:0|max:100',
            'rate_tax_class'  => 'required|in:standard,reduced,zero,exempt',
            'rate_applies_to' => 'required|in:all,physical,digital,shipping',
        ]);

        $data = [
            'tax_zone_id' => $this->activeZoneId,
            'name'         => $this->rate_name,
            'rate'         => $this->rate_rate,
            'tax_class'    => $this->rate_tax_class,
            'is_compound'  => $this->rate_is_compound,
            'is_active'    => $this->rate_is_active,
            'applies_to'   => $this->rate_applies_to,
        ];

        if ($this->editingRateId) {
            TaxRate::findOrFail($this->editingRateId)->update($data);
            $this->dispatch('toast', type: 'success', message: 'Tax rate updated.');
        } else {
            TaxRate::create($data);
            $this->dispatch('toast', type: 'success', message: 'Tax rate added.');
        }

        $this->showRateForm = false;
    }

    public function deleteRate(int $id): void
    {
        TaxRate::findOrFail($id)->delete();
    }

    public function render()
    {
        $zones = TaxZone::with('rates')->orderByDesc('is_default')->orderBy('name')->get();
        return view('livewire.admin.settings.tax-zone-index', compact('zones'));
    }
}
