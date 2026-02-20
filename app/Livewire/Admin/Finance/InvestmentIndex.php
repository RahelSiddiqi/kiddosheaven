<?php

namespace App\Livewire\Admin\Finance;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Investments - Admin')]
class InvestmentIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $typeFilter = '';

    // Modal state
    public bool $showModal = false;

    #[Locked]
    public ?int $editingId = null;

    // Form fields
    public ?int $investor_id = null;
    public string $title = '';
    public string $description = '';
    public string $type = 'inventory';
    public string $amount = '';
    public string $current_value = '';
    public string $investment_date = '';
    public string $status = 'pending';
    public string $notes = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    private function investmentClass(): string
    {
        return class_exists(\App\Domains\Finance\Models\Investment::class)
            ? \App\Domains\Finance\Models\Investment::class
            : \App\Models\Investment::class;
    }

    private function investorClass(): string
    {
        return class_exists(\App\Domains\Finance\Models\Investor::class)
            ? \App\Domains\Finance\Models\Investor::class
            : \App\Models\Investor::class;
    }

    #[Computed]
    public function investors()
    {
        $class = $this->investorClass();
        return $class::where('status', 'active')->orderBy('name')->get();
    }

    #[Computed]
    public function stats(): array
    {
        try {
            $totalInvested = DB::table('investments')
                ->whereIn('status', ['active', 'completed'])
                ->sum('amount');

            $activeCount = DB::table('investments')
                ->where('status', 'active')
                ->count();

            $totalCurrentValue = DB::table('investments')
                ->whereIn('status', ['active', 'completed'])
                ->sum('current_value');

            return [
                'total_invested' => (float) $totalInvested,
                'active_count' => (int) $activeCount,
                'total_current_value' => (float) $totalCurrentValue,
            ];
        } catch (\Exception $e) {
            return [
                'total_invested' => 0,
                'active_count' => 0,
                'total_current_value' => 0,
            ];
        }
    }

    public function createModal(): void
    {
        $this->reset([
            'investor_id', 'title', 'description', 'type', 'amount',
            'current_value', 'investment_date', 'status', 'notes'
        ]);
        $this->type = 'inventory';
        $this->status = 'pending';
        $this->investment_date = now()->format('Y-m-d');
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $class = $this->investmentClass();
        $investment = $class::findOrFail($id);

        $this->editingId = $id;
        $this->investor_id = $investment->investor_id;
        $this->title = $investment->title ?? '';
        $this->description = $investment->description ?? '';
        $this->type = $investment->type ?? 'inventory';
        $this->amount = (string) $investment->amount;
        $this->current_value = (string) ($investment->current_value ?? $investment->amount);
        $this->investment_date = $investment->investment_date?->format('Y-m-d') ?? '';
        $this->status = $investment->status ?? 'pending';
        $this->notes = $investment->notes ?? '';
        $this->showModal = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function save(): void
    {
        $class = $this->investmentClass();

        $rules = [
            'investor_id' => 'required|exists:investors,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:inventory,equipment,property,marketing,research,expansion,working_capital,other',
            'amount' => 'required|numeric|min:0.01',
            'current_value' => 'nullable|numeric|min:0',
            'investment_date' => 'required|date',
            'status' => 'required|in:pending,active,completed,sold',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        $this->validate($rules);

        $data = [
            'investor_id' => $this->investor_id,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'type' => $this->type,
            'amount' => $this->amount,
            'spent_amount' => 0,
            'current_value' => $this->current_value ?: $this->amount,
            'investment_date' => $this->investment_date,
            'status' => $this->status,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            $class::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'Investment updated.', type: 'success');
        } else {
            $class::create($data);
            $this->dispatch('notify', message: 'Investment created.', type: 'success');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function updateStatus(int $id, string $status): void
    {
        $validStatuses = ['pending', 'active', 'completed', 'sold'];
        if (!in_array($status, $validStatuses)) {
            $this->dispatch('notify', message: 'Invalid status.', type: 'error');
            return;
        }

        $class = $this->investmentClass();
        $class::findOrFail($id)->update(['status' => $status]);
        $this->dispatch('notify', message: 'Status updated to ' . ucfirst($status) . '.', type: 'success');
    }

    public function deleteRecord(int $id): void
    {
        $class = $this->investmentClass();
        $investment = $class::findOrFail($id);

        // Check if investment has related purchase batches
        if (method_exists($investment, 'purchaseBatches') && $investment->purchaseBatches()->exists()) {
            $this->dispatch('notify', message: 'Cannot delete: investment has purchase batches.', type: 'error');
            return;
        }

        $investment->delete();
        $this->dispatch('notify', message: 'Investment deleted.', type: 'success');
    }

    public function render()
    {
        $class = $this->investmentClass();

        $investments = $class::with('investor')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', "%{$this->search}%")
                          ->orWhereHas('investor', function ($q) {
                              $q->where('name', 'like', "%{$this->search}%");
                          });
                });
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->latest('investment_date')
            ->paginate(15);

        return view('livewire.admin.finance.investment-index', [
            'investments' => $investments,
        ]);
    }
}
