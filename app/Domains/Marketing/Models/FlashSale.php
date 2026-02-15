<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FlashSale extends Model
{
    protected $fillable = [
        'name',
        'description',
        'discount_percentage',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'discount_percentage' => 'decimal:2',
    ];

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ACTIVE = 'active';
    const STATUS_ENDED = 'ended';

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(\App\Domains\Product\Models\Product::class, 'flash_sale_products')
            ->withPivot('discounted_quantity')
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED
            && $this->starts_at->isFuture();
    }

    public function isEnded(): bool
    {
        return $this->status === self::STATUS_ENDED
            || $this->ends_at->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('starts_at', '>', now());
    }

    public function scopeEnded($query)
    {
        return $query->where('status', self::STATUS_ENDED)
            ->orWhere('ends_at', '<', now());
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->isActive()) {
            return 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400';
        } elseif ($this->isScheduled()) {
            return 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400';
        }
        return 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400';
    }
}
