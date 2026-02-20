<?php

namespace App\Domains\B2B\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Concerns\BelongsToSite;

/**
 * Wholesale / tiered price list assigned to a customer group.
 *
 * A PriceList has many PriceListItems that override the default product price.
 * Falls back to group discount_percent if no item match is found.
 */
class PriceList extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'customer_group_id',
        'name',
        'description',
        'currency',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
                     ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                     ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    // ── Helpers ──────────────────────────────────────────────────

    /** Get the price for a specific product/variant (returns null if not on list) */
    public function priceFor(int $productId, ?int $variantId = null): ?float
    {
        return $this->items()
            ->where('product_id', $productId)
            ->where(fn($q) => $q->whereNull('product_variant_id')
                                ->orWhere('product_variant_id', $variantId))
            ->value('price');
    }
}
