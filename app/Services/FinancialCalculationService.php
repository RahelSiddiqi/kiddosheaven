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
     *
     * Revenue comes from order_items (unit_price × quantity).
     * COGS comes from inventory_movements (unit_cost × ABS(quantity)).
     */
    public function calculateProductProfit(Product $product, Carbon $startDate, Carbon $endDate): array
    {
        // COGS from inventory movements
        $sales = InventoryMovement::where('product_id', $product->id)
            ->where('movement_type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalCost = $sales->sum(fn($sale) => ($sale->unit_cost ?? 0) * abs($sale->quantity));
        $unitsSold = $sales->sum(fn($sale) => abs($sale->quantity));

        // Revenue from order_items for this product in the period
        $totalRevenue = \App\Models\OrderItem::where('product_id', $product->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(unit_price * quantity) as revenue')
            ->value('revenue') ?? 0;

        return [
            'product' => $product,
            'total_revenue' => (float) $totalRevenue,
            'total_cost' => $totalCost,
            'gross_profit' => $totalRevenue - $totalCost,
            'profit_margin' => $totalRevenue > 0 ? (($totalRevenue - $totalCost) / $totalRevenue) * 100 : 0,
            'units_sold' => $unitsSold,
        ];
    }

    /**
     * Calculate category-wise profit.
     */
    public function calculateCategoryProfit(int $categoryId, Carbon $startDate, Carbon $endDate): array
    {
        $products = Product::where('category_id', $categoryId)->get();
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
        $sales = InventoryMovement::where('batch_id', $batch->id)
            ->where('movement_type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $soldQty = $sales->sum(fn($sale) => abs($sale->quantity));
        $totalCost = $soldQty * $batch->unit_cost;

        $remainingValuation = $batch->remaining_quantity * $batch->unit_cost;

        return [
            'batch' => $batch,
            'sold_quantity' => $soldQty,
            'remaining_quantity' => $batch->remaining_quantity,
            'cost' => $totalCost,
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

        // Products linked to this partner via purchase_batches
        $partnerProductIds = PurchaseBatch::where('partner_id', $partner->id)
            ->distinct()
            ->pluck('product_id');

        // COGS: only from batches belonging to this partner
        $partnerBatchIds = PurchaseBatch::where('partner_id', $partner->id)->pluck('id');

        $sales = InventoryMovement::whereIn('batch_id', $partnerBatchIds)
            ->where('movement_type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalSalesCost = $sales->sum(fn($sale) => ($sale->unit_cost ?? 0) * abs($sale->quantity));

        // Revenue from order_items linked to partner's batches
        $totalSalesRevenue = (float) (\App\Models\OrderItem::whereIn('purchase_batch_id', $partnerBatchIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(unit_price * quantity) as revenue')
            ->value('revenue') ?? 0);

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
            ->where('remaining_quantity', '>', 0)
            ->get()
            ->sum(fn($batch) => $batch->remaining_quantity * $batch->unit_cost);
    }

    /**
     * Get complete stock valuation report.
     */
    public function getStockValuationReport(Carbon $startDate, Carbon $endDate): array
    {
        $products = Product::with('category')->get();

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
        // Revenue from order_items (the actual selling prices)
        $salesRevenue = (float) (\App\Models\OrderItem::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(unit_price * quantity) as revenue')
            ->value('revenue') ?? 0);

        // Cost of goods sold from inventory movements
        $cogs = (float) InventoryMovement::where('movement_type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(unit_cost * ABS(quantity)) as total_cogs')
            ->value('total_cogs') ?? 0;

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

    /**
     * Generate a comprehensive financial report for a date range.
     * Called by ReportController::financial().
     */
    public function generateReport(Carbon $startDate, Carbon $endDate): array
    {
        $netProfit = $this->calculateNetProfit($startDate, $endDate);
        $stockValuation = $this->getStockValuationReport($startDate, $endDate);

        // Top products by profit
        $topProducts = \App\Models\OrderItem::whereBetween('created_at', [$startDate, $endDate])
            ->select('product_id')
            ->selectRaw('SUM(unit_price * quantity) as revenue')
            ->selectRaw('SUM(COALESCE(unit_cost, 0) * quantity) as cost')
            ->selectRaw('SUM((unit_price - COALESCE(unit_cost, 0)) * quantity) as profit')
            ->groupBy('product_id')
            ->orderByDesc('profit')
            ->limit(10)
            ->get();

        return [
            'summary' => $netProfit,
            'stock_valuation' => $stockValuation,
            'top_products' => $topProducts,
        ];
    }
}
