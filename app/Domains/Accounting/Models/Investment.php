<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Investment extends Model
{
    protected $fillable = [
        'investor_id',
        'title',
        'description',
        'amount',
        'type',
        'investment_date',
        'expected_return',
        'actual_return',
        'current_value',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'investment_date' => 'date',
        'expected_return' => 'decimal:2',
        'actual_return' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    const TYPE_INVENTORY = 'inventory';
    const TYPE_EQUIPMENT = 'equipment';
    const TYPE_PROPERTY = 'property';
    const TYPE_MARKETING = 'marketing';
    const TYPE_RESEARCH = 'research';
    const TYPE_EXPANSION = 'expansion';
    const TYPE_WORKING_CAPITAL = 'working_capital';
    const TYPE_OTHER = 'other';

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_SOLD = 'sold';
    const STATUS_PENDING = 'pending';

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function getRoiPercentageAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }
        $currentValue = $this->current_value ?? $this->amount;
        return (($currentValue - $this->amount) / $this->amount) * 100;
    }

    public function getExpectedRoiPercentageAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }
        $expected = $this->expected_return ?? 0;
        return (($expected - $this->amount) / $this->amount) * 100;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            'completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            'sold' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
            'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'inventory' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            'equipment' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-400',
            'property' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
            'marketing' => 'bg-pink-100 text-pink-700 dark:bg-pink-500/15 dark:text-pink-400',
            'research' => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
            'expansion' => 'bg-teal-100 text-teal-700 dark:bg-teal-500/15 dark:text-teal-400',
            'working_capital' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-400',
            'other' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }
}
