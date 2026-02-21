<?php

namespace App\Livewire\Admin\Payment;

use App\Domains\Payment\Models\PaymentGateway;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('admin.layouts.app')]
#[Title('Payment Gateways')]
class PaymentGatewayIndex extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    // Form fields
    public string $name       = '';
    public string $code       = '';
    public bool   $is_active  = false;
    public bool   $is_test_mode = true;
    public int    $sort_order = 0;
    public string $icon       = '';
    public string $description = '';
    public string $configJson = '{}';  // raw JSON for config textarea

    protected array $rules = [
        'name'        => 'required|string|max:100',
        'code'        => 'required|string|max:50',
        'sort_order'  => 'integer|min:0',
        'configJson'  => 'nullable|json',
    ];

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'code', 'icon', 'description', 'configJson', 'sort_order']);
        $this->is_active   = false;
        $this->is_test_mode = true;
        $this->configJson  = '{}';
        $this->showForm    = true;
    }

    public function openEdit(int $id): void
    {
        $gw = PaymentGateway::findOrFail($id);
        $this->editingId    = $id;
        $this->name         = $gw->name;
        $this->code         = $gw->code;
        $this->is_active    = (bool) $gw->is_active;
        $this->is_test_mode = (bool) $gw->is_test_mode;
        $this->sort_order   = $gw->sort_order;
        $this->icon         = $gw->icon ?? '';
        $this->description  = $gw->description ?? '';
        // config is encrypted:array — decode to JSON for human editing
        $this->configJson   = json_encode($gw->config ?? [], JSON_PRETTY_PRINT) ?: '{}';
        $this->showForm     = true;
    }

    public function save(): void
    {
        $this->validate();

        $siteId = auth()->user()->site_id ?? \App\Domains\Site\Models\Site::first()?->id;

        $data = [
            'site_id'      => $siteId,
            'name'         => $this->name,
            'code'         => strtolower($this->code),
            'is_active'    => $this->is_active,
            'is_test_mode' => $this->is_test_mode,
            'sort_order'   => $this->sort_order,
            'icon'         => $this->icon ?: null,
            'description'  => $this->description ?: null,
            'config'       => json_decode($this->configJson, true) ?? [],
        ];

        if ($this->editingId) {
            PaymentGateway::findOrFail($this->editingId)->update($data);
            $this->dispatch('toast', type: 'success', message: 'Gateway updated.');
        } else {
            PaymentGateway::create($data);
            $this->dispatch('toast', type: 'success', message: 'Gateway created.');
        }

        $this->showForm = false;
    }

    public function toggle(int $id): void
    {
        $gw = PaymentGateway::findOrFail($id);
        $gw->update(['is_active' => ! $gw->is_active]);
    }

    public function delete(int $id): void
    {
        PaymentGateway::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Gateway removed.');
    }

    public function render()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->orderBy('name')->get();
        return view('livewire.admin.payment.gateway-index', compact('gateways'));
    }
}
