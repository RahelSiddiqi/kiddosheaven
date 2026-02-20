<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'title',
        'description',
        'amount',
        'spent_amount',
        'type',
        'investment_date',
        'current_value',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'investment_date' => 'date',
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

    /**
     * Get the investor for this investment.
     */
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }

    /**
     * Get the purchase batches funded by this investment.
     */
    public function purchaseBatches()
    {
        return $this->hasMany(PurchaseBatch::class);
    }

    /**
     * Get available balance (amount - spent_amount).
     */
    public function getAvailableBalanceAttribute(): float
    {
        return $this->amount - ($this->spent_amount ?? 0);
    }

    /**
     * Get total value of inventory purchased with this investment.
     */
    public function getInventoryValueAttribute(): float
    {
        return $this->purchaseBatches->sum(function ($batch) {
            return $batch->remaining_quantity * $batch->unit_cost;
        });
    }

    /**
     * Auto-calculate current value based on:
     * - Remaining inventory value from batches
     * - Revenue generated from sold inventory
     * This gives a more accurate picture of investment performance
     */
    public function calculateCurrentValue(): float
    {
        // Value still in inventory
        $inventoryValue = $this->purchaseBatches->sum(function ($batch) {
            return $batch->remaining_quantity * $batch->unit_cost;
        });

        // Value already sold (converted to revenue)
        // This is simplified - in a full implementation, you'd track actual revenue per batch
        $soldValue = $this->purchaseBatches->sum(function ($batch) {
            $soldQty = $batch->quantity_received - $batch->remaining_quantity;
            return $soldQty * $batch->unit_cost * 1.3; // Assuming 30% markup as approximation
        });

        return $inventoryValue + $soldValue;
    }

    /**
     * Calculate ROI percentage.
     */
    public function getRoiPercentageAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }
        $currentValue = $this->current_value ?? $this->amount;
        return (($currentValue - $this->amount) / $this->amount) * 100;
    }

    /**
     * Calculate expected ROI percentage.
     * Uses current_value as the expected return.
     */
    public function getExpectedRoiPercentageAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }
        $expected = $this->current_value ?? $this->amount;
        return (($expected - $this->amount) / $this->amount) * 100;
    }

    /**
     * Get badge class for status.
     */
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

    /**
     * Get badge class for type.
     */
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
