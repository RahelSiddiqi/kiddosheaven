<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'transaction_type',
        'payment_method',
        'amount',
        'expense_amount',
        'cost_amount',
        'revenue_amount',
        'reference_type',
        'reference_id',
        'partner_id',
        'investor_id',
        'capital_account_id',
        'description',
        'notes',
        'status',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_amount' => 'decimal:2',
        'cost_amount' => 'decimal:2',
        'revenue_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    const TYPE_CAPITAL_IN = 'capital_in';
    const TYPE_CAPITAL_OUT = 'capital_out';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_SALE = 'sale';
    const TYPE_EXPENSE = 'expense';
    const TYPE_PROFIT_DISTRIBUTION = 'profit_distribution';
    const TYPE_ADJUSTMENT = 'adjustment';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REVERSED = 'reversed';

    const METHOD_CASH = 'cash';
    const METHOD_BANK = 'bank';
    const METHOD_MOBILE_BANKING = 'mobile_banking';
    const METHOD_CHECK = 'check';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = 'TXN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
            }
        });
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class, 'investor_id');
    }

    public function capitalAccount(): BelongsTo
    {
        return $this->belongsTo(CapitalAccount::class, 'capital_account_id');
    }

    public function getCalculatedProfitAttribute(): float
    {
        return $this->revenue_amount - $this->cost_amount - $this->expense_amount;
    }

    public function isInflow(): bool
    {
        return in_array($this->transaction_type, [self::TYPE_CAPITAL_IN, self::TYPE_SALE]);
    }

    public function isOutflow(): bool
    {
        return in_array($this->transaction_type, [self::TYPE_CAPITAL_OUT, self::TYPE_PURCHASE, self::TYPE_EXPENSE]);
    }

    public function getTransactionTypeBadgeClassAttribute(): string
    {
        return match($this->transaction_type) {
            self::TYPE_CAPITAL_IN => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::TYPE_CAPITAL_OUT => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            self::TYPE_PURCHASE => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            self::TYPE_SALE => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::TYPE_EXPENSE => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
            self::TYPE_PROFIT_DISTRIBUTION => 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
            self::TYPE_ADJUSTMENT => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            self::STATUS_REVERSED => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }
}
