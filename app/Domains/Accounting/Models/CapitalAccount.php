<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CapitalAccount extends Model
{
    protected $fillable = [
        'account_number',
        'owner_type',
        'owner_id',
        'type',
        'name',
        'balance',
        'total_credited',
        'total_debited',
        'profit_share_percentage',
        'expense_share_percentage',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_credited' => 'decimal:2',
        'total_debited' => 'decimal:2',
        'profit_share_percentage' => 'decimal:2',
        'expense_share_percentage' => 'decimal:2',
    ];

    const TYPE_CAPITAL_CONTRIBUTION = 'capital_contribution';
    const TYPE_PURCHASE_CONTRIBUTION = 'purchase_contribution';
    const TYPE_PROFIT_SHARE = 'profit_share';
    const TYPE_WITHDRAWAL = 'withdrawal';

    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';
    const STATUS_SUSPENDED = 'suspended';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if (empty($account->account_number)) {
                $account->account_number = 'CAP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
            }
        });
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'capital_account_id');
    }

    public function credit(float $amount, string $description = ''): self
    {
        $this->balance += $amount;
        $this->total_credited += $amount;
        $this->save();

        FinancialTransaction::create([
            'transaction_type' => 'credit',
            'amount' => $amount,
            'description' => $description,
            'capital_account_id' => $this->id,
            'reference_type' => static::class,
            'reference_id' => $this->id,
        ]);

        return $this;
    }

    public function debit(float $amount, string $description = ''): self
    {
        if ($amount > $this->balance) {
            throw new \Exception('Insufficient balance for debit');
        }

        $this->balance -= $amount;
        $this->total_debited += $amount;
        $this->save();

        FinancialTransaction::create([
            'transaction_type' => 'debit',
            'amount' => $amount,
            'description' => $description,
            'capital_account_id' => $this->id,
            'reference_type' => static::class,
            'reference_id' => $this->id,
        ]);

        return $this;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::STATUS_CLOSED => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
            self::STATUS_SUSPENDED => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
