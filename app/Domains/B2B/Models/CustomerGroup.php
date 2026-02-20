<?php

namespace App\Domains\B2B\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Concerns\BelongsToSite;

/**
 * Customer group for B2B / wholesale tiering.
 *
 * Groups: Retail, Wholesale, VIP, Staff, Distributor …
 */
class CustomerGroup extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'name',
        'slug',
        'description',
        'discount_percent',     // blanket % off RRP if no price list
        'is_default',           // auto-assigned to new customers
        'is_active',
        'min_order_amount',     // wholesale minimum order
        'tax_exempt',
        'can_view_prices',      // hide prices until logged in
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_default'       => 'boolean',
        'is_active'        => 'boolean',
        'tax_exempt'       => 'boolean',
        'can_view_prices'  => 'boolean',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(\App\Models\User::class, 'customer_group_id');
    }

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
