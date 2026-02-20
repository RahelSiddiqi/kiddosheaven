<?php

namespace App\Domains\B2B\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Product\Models\Product;

/**
 * Per-product (or per-variant) price override within a price list.
 */
class PriceListItem extends Model
{
    protected $fillable = [
        'price_list_id',
        'product_id',
        'product_variant_id',
        'price',
        'compare_at_price',
        'min_qty',            // minimum qty required to get this price
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'compare_at_price'=> 'decimal:2',
        'min_qty'         => 'integer',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Product\Models\ProductVariant::class, 'product_variant_id');
    }
}
