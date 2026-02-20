<?php

namespace App\Livewire\Admin\Returns;

use App\Domains\Returns\Models\ReturnRequest;
use App\Domains\Returns\Services\ReturnService;
use Livewire\Component;
use Livewire\WithPagination;

class ReturnIndex extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';

    // Approve form
    public ?int   $processingId   = null;
    public float  $refundAmount   = 0;
    public string $refundMethod   = 'original_payment';
    public string $adminNotes     = '';
    public bool   $showApproveForm = false;

    public function openApprove(int $id): void
    {
        $return = ReturnRequest::with('items')->findOrFail($id);
        $this->processingId     = $id;
        $this->refundAmount     = (float) $return->calculateRefundAmount();
        $this->refundMethod     = 'original_payment';
        $this->showApproveForm  = true;
    }

    public function approve(ReturnService $service): void
    {
        $request = ReturnRequest::findOrFail($this->processingId);
        $service->approve($request, $this->refundAmount, $this->refundMethod);

        $this->showApproveForm = false;
        session()->flash('success', 'Return approved.');
    }

    public function decline(int $id, ReturnService $service): void
    {
        $service->decline(ReturnRequest::findOrFail($id));
        session()->flash('success', 'Return declined.');
    }

    public function markReceived(int $id, ReturnService $service): void
    {
        $service->markReceived(ReturnRequest::findOrFail($id));
        session()->flash('success', 'Return marked as received.');
    }

    public function process(int $id, ReturnService $service): void
    {
        $service->process(ReturnRequest::findOrFail($id));
        session()->flash('success', 'Return processed.');
    }

    public function render()
    {
        $returns = ReturnRequest::with(['order', 'user', 'items'])
            ->when($this->search, function ($q) {
                $q->where('return_number', 'like', "%{$this->search}%")
                  ->orWhereHas('order', fn($o) => $o->where('order_number', 'like', "%{$this->search}%"));
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(25);

        return view('livewire.admin.returns.return-index', compact('returns'))
            ->layout('layouts.admin');
    }
}
