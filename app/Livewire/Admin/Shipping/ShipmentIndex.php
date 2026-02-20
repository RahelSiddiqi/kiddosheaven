<?php

namespace App\Livewire\Admin\Shipping;

use App\Domains\Shipping\Models\Shipment;
use App\Domains\Shipping\Services\ShippingService;
use Livewire\Component;
use Livewire\WithPagination;

class ShipmentIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    // Mark shipped form
    public ?int $shippingId    = null;
    public string $trackingNumber = '';
    public string $carrier        = '';
    public bool $showShipForm  = false;

    public function openShipForm(int $id): void
    {
        $this->shippingId     = $id;
        $this->trackingNumber = '';
        $this->carrier        = '';
        $this->showShipForm   = true;
    }

    public function markShipped(ShippingService $service): void
    {
        $this->validate([
            'trackingNumber' => 'nullable|string|max:100',
            'carrier'        => 'nullable|string|max:50',
        ]);

        $shipment = Shipment::findOrFail($this->shippingId);
        $service->markShipped($shipment, $this->trackingNumber ?: null, $this->carrier ?: null);

        $this->showShipForm = false;
        session()->flash('success', 'Shipment marked as shipped.');
    }

    public function markDelivered(int $id, ShippingService $service): void
    {
        $service->markDelivered(Shipment::findOrFail($id));
        session()->flash('success', 'Shipment marked as delivered.');
    }

    public function render()
    {
        $shipments = Shipment::with(['order', 'items'])
            ->when($this->search, function ($q) {
                $q->whereHas('order', fn($o) => $o->where('order_number', 'like', "%{$this->search}%"))
                  ->orWhere('tracking_number', 'like', "%{$this->search}%");
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(25);

        return view('livewire.admin.shipping.shipment-index', compact('shipments'))
            ->layout('layouts.admin');
    }
}
