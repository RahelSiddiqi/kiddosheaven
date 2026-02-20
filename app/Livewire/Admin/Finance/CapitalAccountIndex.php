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
#[Title('Capital Accounts - Admin')]
class CapitalAccountIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $typeFilter = '';

    #[Url]
    public string $statusFilter = '';

    // Modal state
    public bool $showModal = false;

    #[Locked]
    public ?int $editingId = null;

    // Form fields
    public ?int $owner_id = null;
    public string $owner_type = 'partner';
    public string $type = 'capital_contribution';
    public string $name = '';
    public string $balance = '';
    public string $profit_share_percentage = '';
    public string $expense_share_percentage = '';
    public string $status = 'active';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    private function capitalAccountClass(): string
    {
        return class_exists(\App\Domains\Finance\Models\CapitalAccount::class)
            ? \App\Domains\Finance\Models\CapitalAccount::class
            : \App\Models\CapitalAccount::class;
    }

    private function partnerClass(): string
    {
        return class_exists(\App\Domains\Finance\Models\Partner::class)
            ? \App\Domains\Finance\Models\Partner::class
            : \App\Models\Partner::class;
    }

    private function investorClass(): string
    {
        return class_exists(\App\Domains\Finance\Models\Investor::class)
            ? \App\Domains\Finance\Models\Investor::class
            : \App\Models\Investor::class;
    }

    #[Computed]
    public function partners()
    {
        $class = $this->partnerClass();
        return $class::where('status', 'active')->orderBy('name')->get();
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
            $totalAccounts = DB::table('capital_accounts')
                ->where('status', 'active')
                ->count();

            $totalCapital = DB::table('capital_accounts')
                ->where('status', 'active')
                ->sum('balance');

            $totalCredited = DB::table('capital_accounts')
                ->where('status', 'active')
                ->sum('total_credited');

            $totalDebited = DB::table('capital_accounts')
                ->where('status', 'active')
                ->sum('total_debited');

            return [
                'total_accounts' => (int) $totalAccounts,
                'total_capital' => (float) $totalCapital,
                'total_credited' => (float) $totalCredited,
                'total_debited' => (float) $totalDebited,
            ];
        } catch (\Exception $e) {
            return [
                'total_accounts' => 0,
                'total_capital' => 0,
                'total_credited' => 0,
                'total_debited' => 0,
            ];
        }
    }

    public function createModal(): void
    {
        $this->reset([
            'owner_id', 'owner_type', 'type', 'name', 'balance',
            'profit_share_percentage', 'expense_share_percentage', 'status'
        ]);
        $this->owner_type = 'partner';
        $this->type = 'capital_contribution';
        $this->status = 'active';
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $class = $this->capitalAccountClass();
        $account = $class::findOrFail($id);

        $this->editingId = $id;
        $this->owner_id = $account->owner_id;
        $this->owner_type = $account->owner_type === $this->investorClass() ? 'investor' : 'partner';
        $this->type = $account->type ?? 'capital_contribution';
        $this->name = $account->name ?? '';
        $this->balance = (string) $account->balance;
        $this->profit_share_percentage = (string) ($account->profit_share_percentage ?? '');
        $this->expense_share_percentage = (string) ($account->expense_share_percentage ?? '');
        $this->status = $account->status ?? 'active';
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
        $class = $this->capitalAccountClass();

        $rules = [
            'owner_id' => 'required|integer',
            'owner_type' => 'required|in:partner,investor',
            'type' => 'required|in:capital_contribution,purchase_contribution,profit_share,withdrawal',
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
            'profit_share_percentage' => 'nullable|numeric|min:0|max:100',
            'expense_share_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,closed,suspended',
        ];

        $this->validate($rules);

        $ownerTypeClass = $this->owner_type === 'investor'
            ? $this->investorClass()
            : $this->partnerClass();

        $data = [
            'owner_id' => $this->owner_id,
            'owner_type' => $ownerTypeClass,
            'type' => $this->type,
            'name' => $this->name,
            'balance' => $this->balance,
            'profit_share_percentage' => $this->profit_share_percentage ?: null,
            'expense_share_percentage' => $this->expense_share_percentage ?: null,
            'status' => $this->status,
        ];

        if ($this->editingId) {
            $class::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', message: 'Capital account updated.', type: 'success');
        } else {
            $data['total_credited'] = $this->balance > 0 ? $this->balance : 0;
            $data['total_debited'] = 0;
            $class::create($data);
            $this->dispatch('notify', message: 'Capital account created.', type: 'success');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function updateStatus(int $id, string $status): void
    {
        $validStatuses = ['active', 'closed', 'suspended'];
        if (!in_array($status, $validStatuses)) {
            $this->dispatch('notify', message: 'Invalid status.', type: 'error');
            return;
        }

        $class = $this->capitalAccountClass();
        $class::findOrFail($id)->update(['status' => $status]);
        $this->dispatch('notify', message: 'Status updated to ' . ucfirst($status) . '.', type: 'success');
    }

    public function deleteRecord(int $id): void
    {
        $class = $this->capitalAccountClass();
        $account = $class::findOrFail($id);

        // Check if account has transactions
        if (method_exists($account, 'transactions') && $account->transactions()->exists()) {
            $this->dispatch('notify', message: 'Cannot delete: account has transactions.', type: 'error');
            return;
        }

        $account->delete();
        $this->dispatch('notify', message: 'Capital account deleted.', type: 'success');
    }

    public function render()
    {
        $class = $this->capitalAccountClass();

        $accounts = $class::with('owner')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                          ->orWhere('account_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.finance.capital-account-index', [
            'accounts' => $accounts,
        ]);
    }
}
