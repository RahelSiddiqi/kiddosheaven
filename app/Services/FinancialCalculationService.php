<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseBatch;
use App\Models\InventoryMovement;
use App\Models\FinancialTransaction;
use App\Models\CapitalAccount;
use App\Models\Partner;
use App\Models\Investor;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class FinancialCalculationService
{
    /**
     * Calculate product-wise profit.
     */
    public function calculateProductProfit(Product $product, Carbon $startDate, Carbon $endDate): array
    {
        $sales = InventoryMovement::where('product_id', $product->id)
            ->where('movement_type', 'sale')
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->get();

        $totalRevenue = $sales->sum(fn($sale) => $sale->selling_price * abs($sale->quantity));
        $totalCost = $sales->sum('total_cost');

        return [
            'product' => $product,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'gross_profit' => $totalRevenue - $totalCost,
            'profit_margin' => $totalRevenue > 0 ? (($totalRevenue - $totalCost) / $totalRevenue) * 100 : 0,
            'units_sold' => $sales->sum(fn($sale) => abs($sale->quantity)),
        ];
    }

    /**
     * Calculate category-wise profit.
     */
    public function calculateCategoryProfit(int $catalogId, Carbon $startDate, Carbon $endDate): array
    {
        $products = Product::where('catalog_id', $catalogId)->get();
        $totals = [
            'revenue' => 0,
            'cost' => 0,
            'profit' => 0,
            'products' => [],
        ];

        foreach ($products as $product) {
            $productProfit = $this->calculateProductProfit($product, $startDate, $endDate);
            $totals['revenue'] += $productProfit['total_revenue'];
            $totals['cost'] += $productProfit['total_cost'];
            $totals['profit'] += $productProfit['gross_profit'];
            $totals['products'][] = $productProfit;
        }

        $totals['profit_margin'] = $totals['revenue'] > 0 ? ($totals['profit'] / $totals['revenue']) * 100 : 0;

        return $totals;
    }

    /**
     * Calculate batch-wise profit.
     */
    public function calculateBatchProfit(PurchaseBatch $batch, Carbon $startDate, Carbon $endDate): array
    {
        $sales = InventoryMovement::where('purchase_batch_id', $batch->id)
            ->where('movement_type', 'sale')
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->get();

        $soldQty = $sales->sum(fn($sale) => abs($sale->quantity));
        $totalRevenue = $sales->sum(fn($sale) => $sale->selling_price * abs($sale->quantity));
        $totalCost = $soldQty * $batch->unit_cost;

        $remainingValuation = $batch->quantity_remaining * $batch->unit_cost;

        return [
            'batch' => $batch,
            'sold_quantity' => $soldQty,
            'remaining_quantity' => $batch->quantity_remaining,
            'revenue' => $totalRevenue,
            'cost' => $totalCost,
            'gross_profit' => $totalRevenue - $totalCost,
            'remaining_valuation' => $remainingValuation,
        ];
    }

    /**
     * Calculate partner contribution.
     */
    public function calculatePartnerContribution(Partner $partner, Carbon $startDate, Carbon $endDate): array
    {
        $expenses = Expense::where('partner_id', $partner->id)
            ->where('status', 'approved')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        $sales = InventoryMovement::whereHas('product', function ($query) use ($partner) {
                $query->where('partner_id', $partner->id);
            })
            ->where('movement_type', 'sale')
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->get();

        $totalSalesRevenue = $sales->sum(fn($sale) => $sale->selling_price * abs($sale->quantity));
        $totalSalesCost = $sales->sum('total_cost');

        return [
            'partner' => $partner,
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_sales_revenue' => $totalSalesRevenue,
            'total_sales_cost' => $totalSalesCost,
            'gross_profit' => $totalSalesRevenue - $totalSalesCost,
            'expenses' => $expenses,
            'net_contribution' => $totalSalesRevenue - $totalSalesCost - $expenses,
            'commission_rate' => $partner->commission_rate,
            'commission_eligible' => ($totalSalesRevenue - $totalSalesCost) * ($partner->commission_rate / 100),
        ];
    }

    /**
     * Calculate investor ROI.
     */
    public function calculateInvestorROI(Investor $investor, Carbon $startDate, Carbon $endDate): array
    {
        $investments = $investor->investments()
            ->whereBetween('investment_date', [$startDate, $endDate])
            ->get();

        $totalInvested = $investments->sum('amount');
        $currentValue = $investments->sum('current_value');

        $transactions = FinancialTransaction::where('investor_id', $investor->id)
            ->where('transaction_type', 'profit_distribution')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'investor' => $investor,
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_invested' => $totalInvested,
            'current_value' => $currentValue,
            'profit_distribution' => $transactions,
            'total_return' => $currentValue + $transactions - $totalInvested,
            'roi_percentage' => $totalInvested > 0 ? (($currentValue + $transactions - $totalInvested) / $totalInvested) * 100 : 0,
        ];
    }

    /**
     * Calculate stock valuation.
     */
    public function calculateStockValuation(Product $product): float
    {
        return PurchaseBatch::where('product_id', $product->id)
            ->where('quantity_remaining', '>', 0)
            ->get()
            ->sum(fn($batch) => $batch->quantity_remaining * $batch->unit_cost);
    }

    /**
     * Get complete stock valuation report.
     */
    public function getStockValuationReport(Carbon $startDate, Carbon $endDate): array
    {
        $products = Product::with('catalog')->get();

        $report = [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'total_valuation' => 0,
            'total_cost' => 0,
            'products' => [],
        ];

        foreach ($products as $product) {
            $batches = PurchaseBatch::where('product_id', $product->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $quantity = $batches->sum('quantity_received');
            $valuation = $batches->sum(fn($b) => $b->quantity_received * $b->unit_cost);

            $report['total_cost'] += $valuation;
            $report['products'][] = [
                'product' => $product,
                'quantity' => $quantity,
                'valuation' => $valuation,
            ];
        }

        return $report;
    }

    /**
     * Calculate net profit after expenses.
     */
    public function calculateNetProfit(Carbon $startDate, Carbon $endDate): array
    {
        // Revenue from sales
        $salesRevenue = InventoryMovement::where('movement_type', 'sale')
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->sum(DB::raw('selling_price * ABS(quantity)'));

        // Cost of goods sold
        $cogs = InventoryMovement::where('movement_type', 'sale')
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->sum('total_cost');

        // Gross profit
        $grossProfit = $salesRevenue - $cogs;

        // Operating expenses
        $expenses = Expense::where('status', 'approved')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Net profit
        $netProfit = $grossProfit - $expenses;

        // Partner profit shares
        $partnerShares = FinancialTransaction::where('transaction_type', 'profit_distribution')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Net profit after distributions
        $netProfitAfterDistributions = $netProfit - $partnerShares;

        return [
            'period' => ['start' => $startDate, 'end' => $endDate],
            'gross_revenue' => $salesRevenue,
            'cost_of_goods_sold' => $cogs,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $expenses,
            'net_profit' => $netProfit,
            'partner_distributions' => $partnerShares,
            'net_profit_after_distributions' => $netProfitAfterDistributions,
            'profit_margin' => $salesRevenue > 0 ? ($netProfit / $salesRevenue) * 100 : 0,
        ];
    }
}
