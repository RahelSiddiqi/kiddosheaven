<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\Investor;
use App\Models\CapitalAccount;
use App\Models\FinancialTransaction;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PartnerInvestorService
{
    protected $financialCalcService;

    public function __construct(FinancialCalculationService $financialCalcService)
    {
        $this->financialCalcService = $financialCalcService;
    }

    /**
     * Create or get capital account for partner.
     */
    public function getOrCreatePartnerCapitalAccount(Partner $partner, string $type = 'capital_contribution'): CapitalAccount
    {
        return CapitalAccount::firstOrCreate(
            [
                'owner_type' => Partner::class,
                'owner_id' => $partner->id,
                'type' => $type,
            ],
            [
                'name' => "{$partner->name} - {$type}",
                'balance' => 0,
                'total_credited' => 0,
                'total_debited' => 0,
                'status' => 'active',
            ]
        );
    }

    /**
     * Create or get capital account for investor.
     */
    public function getOrCreateInvestorCapitalAccount(Investor $investor, string $type = 'capital_contribution'): CapitalAccount
    {
        return CapitalAccount::firstOrCreate(
            [
                'owner_type' => Investor::class,
                'owner_id' => $investor->id,
                'type' => $type,
            ],
            [
                'name' => "{$investor->name} - {$type}",
                'balance' => 0,
                'total_credited' => 0,
                'total_debited' => 0,
                'status' => 'active',
            ]
        );
    }

    /**
     * Record capital contribution.
     */
    public function recordCapitalContribution(object $owner, float $amount, string $description = ''): CapitalAccount
    {
        $account = $owner instanceof Partner
            ? $this->getOrCreatePartnerCapitalAccount($owner)
            : $this->getOrCreateInvestorCapitalAccount($owner);

        return DB::transaction(function () use ($account, $amount, $description, $owner) {
            $account->credit($amount, $description);

            FinancialTransaction::create([
                'transaction_type' => 'capital_in',
                'amount' => $amount,
                'owner_type' => get_class($owner),
                'owner_id' => $owner->id,
                'capital_account_id' => $account->id,
                'description' => $description,
            ]);

            return $account->refresh();
        });
    }

    /**
     * Record withdrawal.
     */
    public function recordWithdrawal(object $owner, float $amount, string $description = ''): CapitalAccount
    {
        $account = $owner instanceof Partner
            ? $this->getOrCreatePartnerCapitalAccount($owner)
            : $this->getOrCreateInvestorCapitalAccount($owner);

        return DB::transaction(function () use ($account, $amount, $description, $owner) {
            $account->debit($amount, $description);

            FinancialTransaction::create([
                'transaction_type' => 'capital_out',
                'amount' => $amount,
                'owner_type' => get_class($owner),
                'owner_id' => $owner->id,
                'capital_account_id' => $account->id,
                'description' => $description,
            ]);

            return $account->refresh();
        });
    }

    /**
     * Calculate and distribute profit to partners and investors.
     */
    public function distributeProfit(float $totalProfit, Carbon $distributionDate): array
    {
        $partners = Partner::where('status', 'active')
            ->where('profit_share_percentage', '>', 0)
            ->get();

        $investors = Investor::where('status', 'active')
            ->whereHas('investments', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        $distributions = [];

        // Calculate total profit share percentages
        $totalPartnerShare = $partners->sum('profit_share_percentage');
        $totalInvestorShare = $investors->sum(function ($inv) {
            return $inv->investments->sum(function ($investment) use ($inv) {
                return $investment->amount / $inv->investments->sum('amount') * 100;
            });
        });

        // Distribute to partners
        foreach ($partners as $partner) {
            $sharePercentage = $totalPartnerShare > 0
                ? ($partner->profit_share_percentage / $totalPartnerShare) * (1 - ($totalInvestorShare / 100))
                : 0;

            $shareAmount = $totalProfit * ($sharePercentage / 100);

            if ($shareAmount > 0) {
                $account = $this->getOrCreatePartnerCapitalAccount($partner, 'profit_share');
                $account->credit($shareAmount, "Profit distribution for period ending {$distributionDate->format('Y-m-d')}");

                FinancialTransaction::create([
                    'transaction_type' => 'profit_distribution',
                    'amount' => $shareAmount,
                    'partner_id' => $partner->id,
                    'capital_account_id' => $account->id,
                    'description' => "Profit distribution - {$distributionDate->format('F Y')}",
                    'transaction_date' => $distributionDate,
                ]);

                $distributions[] = [
                    'type' => 'partner',
                    'entity' => $partner,
                    'share_percentage' => $partner->profit_share_percentage,
                    'amount' => $shareAmount,
                ];
            }
        }

        // Distribute to investors based on investment proportion
        $totalInvestmentValue = $investors->sum(function ($investor) {
            return $investor->investments->where('status', 'active')->sum('current_value');
        });

        foreach ($investors as $investor) {
            $investmentValue = $investor->investments->where('status', 'active')->sum('current_value');
            $proportion = $totalInvestmentValue > 0 ? ($investmentValue / $totalInvestmentValue) : 0;
            $investorSharePercentage = $totalInvestorShare > 0
                ? $proportion * (1 - ($totalPartnerShare / 100))
                : 0;

            $shareAmount = $totalProfit * ($investorSharePercentage / 100);

            if ($shareAmount > 0) {
                $account = $this->getOrCreateInvestorCapitalAccount($investor, 'profit_share');
                $account->credit($shareAmount, "Profit distribution for period ending {$distributionDate->format('Y-m-d')}");

                FinancialTransaction::create([
                    'transaction_type' => 'profit_distribution',
                    'amount' => $shareAmount,
                    'investor_id' => $investor->id,
                    'capital_account_id' => $account->id,
                    'description' => "Profit distribution - {$distributionDate->format('F Y')}",
                    'transaction_date' => $distributionDate,
                ]);

                $distributions[] = [
                    'type' => 'investor',
                    'entity' => $investor,
                    'investment_value' => $investmentValue,
                    'share_percentage' => $investorSharePercentage * 100,
                    'amount' => $shareAmount,
                ];
            }
        }

        return $distributions;
    }

    /**
     * Get partner account summary.
     */
    public function getPartnerAccountSummary(Partner $partner): array
    {
        $account = $this->getOrCreatePartnerCapitalAccount($partner);

        return [
            'partner' => $partner,
            'account' => $account,
            'contributions' => $account->total_credited,
            'withdrawals' => $account->total_debited,
            'current_balance' => $account->balance,
            'contribution_rate' => $partner->profit_share_percentage,
        ];
    }

    /**
     * Get investor account summary.
     */
    public function getInvestorAccountSummary(Investor $investor): array
    {
        $account = $this->getOrCreateInvestorCapitalAccount($investor);

        $totalInvested = $investor->investments->sum('amount');
        $currentValue = $investor->investments->sum('current_value');

        return [
            'investor' => $investor,
            'account' => $account,
            'total_invested' => $totalInvested,
            'current_value' => $currentValue,
            'current_balance' => $account->balance,
            'total_return' => $currentValue - $totalInvested,
            'roi_percentage' => $totalInvested > 0 ? (($currentValue - $totalInvested) / $totalInvested) * 100 : 0,
        ];
    }
}
