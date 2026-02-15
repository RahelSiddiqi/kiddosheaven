<?php

namespace App\Domains\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investor extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'nid_or_passport',
        'total_invested',
        'current_value',
        'type',
        'contact_person',
        'contact_email',
        'contact_phone',
        'notes',
        'status',
    ];

    protected $casts = [
        'total_invested' => 'decimal:2',
        'current_value' => 'decimal:2',
    ];

    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_COMPANY = 'company';
    const TYPE_VENTURE_CAPITAL = 'venture_capital';
    const TYPE_ANGEL = 'angel';
    const TYPE_INSTITUTION = 'institution';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function getTotalReturnsAttribute(): float
    {
        return $this->current_value - $this->total_invested;
    }

    public function getRoiPercentageAttribute(): float
    {
        if ($this->total_invested <= 0) {
            return 0;
        }
        return (($this->current_value - $this->total_invested) / $this->total_invested) * 100;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::STATUS_INACTIVE => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            self::TYPE_INDIVIDUAL => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            self::TYPE_COMPANY => 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
            self::TYPE_VENTURE_CAPITAL => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
            self::TYPE_ANGEL => 'bg-pink-100 text-pink-700 dark:bg-pink-500/15 dark:text-pink-400',
            self::TYPE_INSTITUTION => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }
}
