<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Reports - Admin')]
class ReportsDashboard extends Component
{
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        // Default to current month
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $totalExpenses = $this->getTotalExpenses();
        $totalRevenue = $this->getTotalRevenue();
        $totalInvestments = $this->getTotalInvestments();
        $netProfit = $totalRevenue - $totalExpenses;

        return view('livewire.admin.reports.reports-dashboard', [
            'totalExpenses' => $totalExpenses,
            'totalRevenue' => $totalRevenue,
            'totalInvestments' => $totalInvestments,
            'netProfit' => $netProfit,
        ]);
    }

    protected function getTotalExpenses(): float
    {
        try {
            $query = DB::table('expenses')
                ->where('status', 'approved');

            if ($this->dateFrom) {
                $query->whereDate('expense_date', '>=', $this->dateFrom);
            }
            if ($this->dateTo) {
                $query->whereDate('expense_date', '<=', $this->dateTo);
            }

            return (float) $query->sum('amount');
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getTotalRevenue(): float
    {
        try {
            // Try Domains model first
            try {
                $model = \App\Domains\Order\Models\Order::class;
                $query = $model::withoutGlobalScope('site')
                    ->where('status', 'delivered');
            } catch (\Exception $e) {
                $model = \App\Models\Order::class;
                $query = $model::withoutGlobalScope('site')
                    ->where('status', 'delivered');
            }

            if ($this->dateFrom) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            }
            if ($this->dateTo) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            }

            return (float) $query->sum('total_amount');
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getTotalInvestments(): float
    {
        try {
            $query = DB::table('investments');

            if ($this->dateFrom) {
                $query->whereDate('investment_date', '>=', $this->dateFrom);
            }
            if ($this->dateTo) {
                $query->whereDate('investment_date', '<=', $this->dateTo);
            }

            return (float) $query->sum('amount');
        } catch (\Exception $e) {
            return 0;
        }
    }
}
