<?php

namespace App\Livewire\Admin\Accounting;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Accounting - Admin')]
class AccountingOverview extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    // Category management modal
    public bool   $showCategoryModal = false;
    public ?int   $editingCategoryId = null;
    public string $categoryName      = '';
    public string $categoryIcon      = '';
    public string $categoryColor     = '#6366f1';

    // Status filters
    #[Url]
    public string $statusFilter   = '';
    #[Url]
    public string $categoryFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function approveExpense(int $id): void
    {
        $model = class_exists(\App\Domains\Accounting\Models\Expense::class)
            ? \App\Domains\Accounting\Models\Expense::class
            : \App\Models\Expense::class;
        $model::findOrFail($id)->update(['status' => 'approved']);
        $this->dispatch('notify', message: 'Expense approved.', type: 'success');
    }

    public function rejectExpense(int $id): void
    {
        $model = class_exists(\App\Domains\Accounting\Models\Expense::class)
            ? \App\Domains\Accounting\Models\Expense::class
            : \App\Models\Expense::class;
        $model::findOrFail($id)->update(['status' => 'rejected']);
        $this->dispatch('notify', message: 'Expense rejected.', type: 'error');
    }

    public function deleteExpense(int $id): void
    {
        $model = class_exists(\App\Domains\Accounting\Models\Expense::class)
            ? \App\Domains\Accounting\Models\Expense::class
            : \App\Models\Expense::class;
        $expense = $model::findOrFail($id);
        if ($expense->receipt_url) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($expense->receipt_url);
        }
        $expense->delete();
        $this->dispatch('notify', message: 'Expense deleted.', type: 'success');
    }

    public function openCategoryModal(?int $id = null): void
    {
        $this->reset(['categoryName', 'categoryIcon', 'categoryColor', 'editingCategoryId']);
        $this->categoryColor = '#6366f1';
        if ($id) {
            $cat = \App\Models\ExpenseCategory::findOrFail($id);
            $this->editingCategoryId = $id;
            $this->categoryName  = $cat->name;
            $this->categoryIcon  = $cat->icon ?? '';
            $this->categoryColor = $cat->color ?? '#6366f1';
        }
        $this->showCategoryModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate(['categoryName' => 'required|string|max:255']);
        $data = ['name' => $this->categoryName, 'icon' => $this->categoryIcon ?: null, 'color' => $this->categoryColor, 'is_active' => true];
        if ($this->editingCategoryId) {
            \App\Models\ExpenseCategory::findOrFail($this->editingCategoryId)->update($data);
        } else {
            \App\Models\ExpenseCategory::create($data);
        }
        $this->showCategoryModal = false;
        $this->dispatch('notify', message: 'Category saved.', type: 'success');
    }

    public function deleteCategory(int $id): void
    {
        $cat = \App\Models\ExpenseCategory::findOrFail($id);
        if ($cat->expenses()->exists()) {
            $this->dispatch('notify', message: 'Cannot delete: category has expenses.', type: 'error');
            return;
        }
        $cat->delete();
        $this->dispatch('notify', message: 'Category deleted.', type: 'success');
    }

    public function render()
    {
        $stats = $this->getStats();
        $expenses = $this->getExpenses();
        $categories = $this->getCategories();

        return view('livewire.admin.accounting.accounting-overview', [
            'totalApproved' => $stats['approved'],
            'pendingApproval' => $stats['pending'],
            'thisMonth' => $stats['this_month'],
            'expenses' => $expenses,
            'categories' => $categories,
        ]);
    }

    protected function getCategories()
    {
        try {
            return \App\Models\ExpenseCategory::orderBy('name')->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    protected function getStats(): array
    {
        try {
            $approved = DB::table('expenses')
                ->where('status', 'approved')
                ->sum('amount');

            $pending = DB::table('expenses')
                ->where('status', 'pending')
                ->sum('amount');

            $thisMonth = DB::table('expenses')
                ->where('status', 'approved')
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount');

            return [
                'approved' => (float) $approved,
                'pending' => (float) $pending,
                'this_month' => (float) $thisMonth,
            ];
        } catch (\Exception $e) {
            return [
                'approved' => 0,
                'pending' => 0,
                'this_month' => 0,
            ];
        }
    }

    protected function getExpenses()
    {
        try {
            // Try to use Eloquent model for relationships
            try {
                $model = \App\Domains\Expense\Models\Expense::class;
                $query = $model::query();
            } catch (\Exception $e) {
                try {
                    $model = \App\Models\Expense::class;
                    $query = $model::query();
                } catch (\Exception $e) {
                    // Fallback to DB query builder
                    return $this->getExpensesFromDb();
                }
            }

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            if ($this->categoryFilter) {
                $query->where('category_id', $this->categoryFilter);
            }

            return $query->with('category')
                ->latest('expense_date')
                ->paginate(10);
        } catch (\Exception $e) {
            return $this->getExpensesFromDb();
        }
    }

    protected function getExpensesFromDb()
    {
        try {
            $query = DB::table('expenses')
                ->leftJoin('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
                ->select('expenses.*', 'expense_categories.name as category_name');

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('expenses.title', 'like', '%' . $this->search . '%')
                      ->orWhere('expenses.description', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter) {
                $query->where('expenses.status', $this->statusFilter);
            }

            if ($this->categoryFilter) {
                $query->where('expenses.category_id', $this->categoryFilter);
            }

            return $query->orderByDesc('expenses.expense_date')
                ->paginate(10);
        } catch (\Exception $e) {
            return collect()->paginate(10);
        }
    }
}
