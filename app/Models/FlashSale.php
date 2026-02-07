<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    use HasFactory;

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

    public function products()
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products')
            ->withPivot('discounted_quantity')
            ->withTimestamps();
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function isScheduled()
    {
        return $this->status === self::STATUS_SCHEDULED
            && $this->starts_at->isFuture();
    }

    public function isEnded()
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

    public function getStatusBadgeAttribute()
    {
        if ($this->isActive()) {
            return '<span class="badge bg-green-100 text-green-700">Active</span>';
        } elseif ($this->isScheduled()) {
            return '<span class="badge bg-blue-100 text-blue-700">Scheduled</span>';
        } else {
            return '<span class="badge bg-gray-100 text-gray-700">Ended</span>';
        }
    }
}
