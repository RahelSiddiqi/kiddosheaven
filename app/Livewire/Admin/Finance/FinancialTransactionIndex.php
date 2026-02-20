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
#[Title('Financial Transactions - Admin')]
class FinancialTransactionIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $typeFilter = '';

    #[Url]
    public string $accountFilter = '';

    #[Url]
    public string $statusFilter = '';

    // Modal state
    public bool $showModal = false;

    #[Locked]
    public ?int $editingId = null;

    // Form fields
    public ?int $capital_account_id = null;
    public string $transaction_type = 'capital_in';
    public string $payment_method = 'bank';
    public string $amount = '';
    public string $description = '';
    public string $notes = '';
    public string $transaction_date = '';
    public string $status = 'completed';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedAccountFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    private function transactionClass(): string
    {
        return class_exists(\App\Domains\Finance\Models\FinancialTransaction::class)
            ? \App\Domains\Finance\Models\FinancialTransaction::class
            : \App\Models\FinancialTransaction::class;
    }

    private function capitalAccountClass(): string
    {
        return class_exists(\App\Domains\Finance\Models\CapitalAccount::class)
            ? \App\Domains\Finance\Models\CapitalAccount::class
            : \App\Models\CapitalAccount::class;
    }

    #[Computed]
    public function capitalAccounts()
    {
        $class = $this->capitalAccountClass();
        return $class::with('owner')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function stats(): array
    {
        try {
            $totalDebit = DB::table('financial_transactions')
                ->whereIn('transaction_type', ['capital_out', 'purchase', 'expense'])
                ->where('status', 'completed')
                ->sum('amount');

            $totalCredit = DB::table('financial_transactions')
                ->whereIn('transaction_type', ['capital_in', 'sale'])
                ->where('status', 'completed')
                ->sum('amount');

            $netBalance = $totalCredit - $totalDebit;

            $thisMonthTotal = DB::table('financial_transactions')
                ->where('status', 'completed')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount');

            return [
                'total_debit' => (float) $totalDebit,
                'total_credit' => (float) $totalCredit,
                'net_balance' => (float) $netBalance,
                'this_month' => (float) $thisMonthTotal,
            ];
        } catch (\Exception $e) {
            return [
                'total_debit' => 0,
                'total_credit' => 0,
                'net_balance' => 0,
                'this_month' => 0,
            ];
        }
    }

    public function createModal(): void
    {
        $this->reset([
            'capital_account_id', 'transaction_type', 'payment_method',
            'amount', 'description', 'notes', 'transaction_date', 'status'
        ]);
        $this->transaction_type = 'capital_in';
        $this->payment_method = 'bank';
        $this->status = 'completed';
        $this->transaction_date = now()->format('Y-m-d');
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $class = $this->transactionClass();
        $transaction = $class::findOrFail($id);

        $this->editingId = $id;
        $this->capital_account_id = $transaction->capital_account_id;
        $this->transaction_type = $transaction->transaction_type ?? 'capital_in';
        $this->payment_method = $transaction->payment_method ?? 'bank';
        $this->amount = (string) $transaction->amount;
        $this->description = $transaction->description ?? '';
        $this->notes = $transaction->notes ?? '';
        $this->transaction_date = $transaction->transaction_date?->format('Y-m-d') ?? '';
        $this->status = $transaction->status ?? 'completed';
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
        $class = $this->transactionClass();
        $accountClass = $this->capitalAccountClass();

        $rules = [
            'capital_account_id' => 'nullable|exists:capital_accounts,id',
            'transaction_type' => 'required|in:capital_in,capital_out,purchase,sale,expense,profit_distribution,adjustment',
            'payment_method' => 'required|in:cash,bank,mobile_banking,check',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'transaction_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled,reversed',
        ];

        $this->validate($rules);

        DB::beginTransaction();
        try {
            $data = [
                'capital_account_id' => $this->capital_account_id,
                'transaction_type' => $this->transaction_type,
                'payment_method' => $this->payment_method,
                'amount' => $this->amount,
                'description' => $this->description ?: null,
                'notes' => $this->notes ?: null,
                'transaction_date' => $this->transaction_date,
                'status' => $this->status,
            ];

            if ($this->editingId) {
                $transaction = $class::findOrFail($this->editingId);
                $oldAmount = (float) $transaction->amount;
                $oldType = $transaction->transaction_type;
                $oldAccountId = $transaction->capital_account_id;

                // Reverse old balance change if account changed or amount changed
                if ($oldAccountId && ($oldAccountId != $this->capital_account_id || $oldAmount != (float)$this->amount)) {
                    $oldAccount = $accountClass::find($oldAccountId);
                    if ($oldAccount) {
                        if (in_array($oldType, ['capital_in', 'sale'])) {
                            $oldAccount->balance -= $oldAmount;
                            $oldAccount->total_credited -= $oldAmount;
                        } else {
                            $oldAccount->balance += $oldAmount;
                            $oldAccount->total_debited -= $oldAmount;
                        }
                        $oldAccount->save();
                    }

                    // Apply new balance change
                    if ($this->capital_account_id) {
                        $newAccount = $accountClass::find($this->capital_account_id);
                        if ($newAccount) {
                            if (in_array($this->transaction_type, ['capital_in', 'sale'])) {
                                $newAccount->balance += (float)$this->amount;
                                $newAccount->total_credited += (float)$this->amount;
                            } else {
                                $newAccount->balance -= (float)$this->amount;
                                $newAccount->total_debited += (float)$this->amount;
                            }
                            $newAccount->save();
                        }
                    }
                }

                $transaction->update($data);
                $this->dispatch('notify', message: 'Transaction updated.', type: 'success');
            } else {
                $transaction = $class::create($data);

                // Update capital account balance if linked
                if ($this->capital_account_id) {
                    $account = $accountClass::find($this->capital_account_id);
                    if ($account) {
                        if (in_array($this->transaction_type, ['capital_in', 'sale'])) {
                            $account->balance += (float)$this->amount;
                            $account->total_credited += (float)$this->amount;
                        } else {
                            $account->balance -= (float)$this->amount;
                            $account->total_debited += (float)$this->amount;
                        }
                        $account->save();
                    }
                }

                $this->dispatch('notify', message: 'Transaction recorded.', type: 'success');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', message: 'Error: ' . $e->getMessage(), type: 'error');
            return;
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function updateStatus(int $id, string $status): void
    {
        $validStatuses = ['pending', 'completed', 'cancelled', 'reversed'];
        if (!in_array($status, $validStatuses)) {
            $this->dispatch('notify', message: 'Invalid status.', type: 'error');
            return;
        }

        $class = $this->transactionClass();
        $class::findOrFail($id)->update(['status' => $status]);
        $this->dispatch('notify', message: 'Status updated to ' . ucfirst($status) . '.', type: 'success');
    }

    public function deleteRecord(int $id): void
    {
        $class = $this->transactionClass();
        $accountClass = $this->capitalAccountClass();

        DB::beginTransaction();
        try {
            $transaction = $class::findOrFail($id);

            // Reverse balance if transaction was completed
            if ($transaction->status === 'completed' && $transaction->capital_account_id) {
                $account = $accountClass::find($transaction->capital_account_id);
                if ($account) {
                    if (in_array($transaction->transaction_type, ['capital_in', 'sale'])) {
                        $account->balance -= (float)$transaction->amount;
                        $account->total_credited -= (float)$transaction->amount;
                    } else {
                        $account->balance += (float)$transaction->amount;
                        $account->total_debited -= (float)$transaction->amount;
                    }
                    $account->save();
                }
            }

            $transaction->delete();
            DB::commit();
            $this->dispatch('notify', message: 'Transaction deleted.', type: 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', message: 'Error deleting transaction.', type: 'error');
        }
    }

    public function render()
    {
        $class = $this->transactionClass();

        $transactions = $class::with(['capitalAccount.owner'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('description', 'like', "%{$this->search}%")
                          ->orWhere('transaction_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->typeFilter, fn($q) => $q->where('transaction_type', $this->typeFilter))
            ->when($this->accountFilter, fn($q) => $q->where('capital_account_id', $this->accountFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest('transaction_date')
            ->paginate(15);

        return view('livewire.admin.finance.financial-transaction-index', [
            'transactions' => $transactions,
        ]);
    }
}
