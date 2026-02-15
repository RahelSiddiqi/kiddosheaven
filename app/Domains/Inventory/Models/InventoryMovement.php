<?php

namespace App\Domains\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'movement_number',
        'product_id',
        'product_variant_id',
        'batch_id',
        'inventory_item_id',
        'movement_type',
        'quantity',
        'unit_cost',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    const TYPE_PURCHASE = 'purchase';
    const TYPE_SALE = 'sale';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_RETURN = 'return';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_DAMAGE = 'damage';
    const TYPE_EXPIRE = 'expire';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movement) {
            if (empty($movement->movement_number)) {
                $movement->movement_number = 'MOV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Product\Models\Product::class);
    }

    public function purchaseBatch(): BelongsTo
    {
        return $this->belongsTo(PurchaseBatch::class, 'batch_id');
    }

    public function batch(): BelongsTo
    {
        return $this->purchaseBatch();
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Product\Models\ProductVariant::class, 'product_variant_id');
    }

    public function isInflow(): bool
    {
        return in_array($this->movement_type, [self::TYPE_PURCHASE, self::TYPE_RETURN]);
    }

    public function isOutflow(): bool
    {
        return in_array($this->movement_type, [self::TYPE_SALE, self::TYPE_DAMAGE, self::TYPE_EXPIRE]);
    }

    public function getProfitAttribute(): ?float
    {
        if ($this->movement_type !== self::TYPE_SALE) {
            return null;
        }

        $cost = $this->unit_cost ?? 0;
        $qty  = abs($this->quantity);

        if ($this->reference_type === \App\Domains\Order\Models\Order::class && $this->reference_id) {
            $orderItem = \App\Domains\Order\Models\OrderItem::where('order_id', $this->reference_id)
                ->where('product_id', $this->product_id)
                ->first();

            if ($orderItem) {
                return ($orderItem->unit_price - $cost) * $qty;
            }
        }

        return null;
    }

    public function getMovementTypeBadgeClassAttribute(): string
    {
        return match($this->movement_type) {
            self::TYPE_PURCHASE => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
            self::TYPE_SALE => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
            self::TYPE_ADJUSTMENT => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
            self::TYPE_RETURN => 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
            self::TYPE_TRANSFER => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
            self::TYPE_DAMAGE => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
            self::TYPE_EXPIRE => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
        };
    }

    public function scopePurchases($query)
    {
        return $query->where('movement_type', self::TYPE_PURCHASE);
    }

    public function scopeSales($query)
    {
        return $query->where('movement_type', self::TYPE_SALE);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
