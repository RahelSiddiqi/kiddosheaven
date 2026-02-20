<?php

namespace App\Livewire\Admin\Accounting;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('admin.layouts.app')]
#[Title('Expense Form - Admin')]
class ExpenseForm extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?int $expenseId = null;

    public string  $title       = '';
    public string  $description = '';
    public string  $amount      = '';
    public string  $expenseDate = '';
    public ?int    $categoryId  = null;
    public ?int    $partnerId   = null;
    public string  $status      = 'pending';

    public ?string $existingReceipt = null;
    public $receipt = null; // TemporaryUploadedFile

    // populated in mount
    public array $categories = [];
    public array $partners   = [];

    public function mount(?int $expenseId = null): void
    {
        $this->expenseId  = $expenseId;
        $this->expenseDate = now()->format('Y-m-d');
        $this->loadOptions();

        if ($expenseId) {
            $this->loadExpense($expenseId);
        }
    }

    private function loadOptions(): void
    {
        $this->categories = \App\Models\ExpenseCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        // Partner model may not exist or may not have status field
        try {
            $partnerClass = class_exists(\App\Domains\Accounting\Models\Partner::class)
                ? \App\Domains\Accounting\Models\Partner::class
                : \App\Models\Partner::class;

            $this->partners = $partnerClass::where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray();
        } catch (\Exception $e) {
            $this->partners = [];
        }
    }

    private function loadExpense(int $id): void
    {
        $model = class_exists(\App\Domains\Accounting\Models\Expense::class)
            ? \App\Domains\Accounting\Models\Expense::class
            : \App\Models\Expense::class;

        $expense = $model::findOrFail($id);

        $this->title           = $expense->title;
        $this->description     = $expense->description ?? '';
        $this->amount          = (string) $expense->amount;
        $this->expenseDate     = $expense->expense_date
            ? (is_string($expense->expense_date) ? $expense->expense_date : $expense->expense_date->format('Y-m-d'))
            : now()->format('Y-m-d');
        $this->categoryId      = $expense->category_id;
        $this->partnerId       = $expense->partner_id;
        $this->status          = $expense->status ?? 'pending';
        $this->existingReceipt = $expense->receipt_url;
    }

    public function removeReceipt(): void
    {
        if ($this->existingReceipt) {
            Storage::disk('public')->delete($this->existingReceipt);
            $this->existingReceipt = null;
        }
        $this->receipt = null;
    }

    public function save(): void
    {
        $this->validate([
            'title'       => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'expenseDate' => 'required|date',
            'categoryId'  => 'required|exists:expense_categories,id',
            'partnerId'   => 'nullable|exists:partners,id',
            'status'      => 'in:pending,approved,rejected',
            'receipt'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $receiptPath = $this->existingReceipt;

        if ($this->receipt) {
            if ($receiptPath) {
                Storage::disk('public')->delete($receiptPath);
            }
            $receiptPath = $this->receipt->store('receipts', 'public');
        }

        $model = class_exists(\App\Domains\Accounting\Models\Expense::class)
            ? \App\Domains\Accounting\Models\Expense::class
            : \App\Models\Expense::class;

        $data = [
            'title'        => $this->title,
            'description'  => $this->description ?: null,
            'amount'       => (float) $this->amount,
            'expense_date' => $this->expenseDate,
            'category_id'  => $this->categoryId,
            'partner_id'   => $this->partnerId ?: null,
            'status'       => $this->status,
            'receipt_url'  => $receiptPath,
        ];

        if ($this->expenseId) {
            $model::findOrFail($this->expenseId)->update($data);
            $this->dispatch('notify', message: 'Expense updated.', type: 'success');
        } else {
            $data['created_by'] = auth()->id();
            $model::create($data);
            $this->dispatch('notify', message: 'Expense created.', type: 'success');
        }

        $this->redirect(route('admin.expenses.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.accounting.expense-form');
    }
}
