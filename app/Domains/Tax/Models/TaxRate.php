<?php

namespace App\Domains\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A specific tax rate within a tax zone.
 * A zone can have multiple rates (e.g. VAT 15% + Municipality Tax 2%).
 */
class TaxRate extends Model
{
    protected $fillable = [
        'tax_zone_id',
        'name',           // e.g. "VAT", "Service Tax"
        'rate',           // decimal: 15.00 = 15%
        'tax_class',      // standard | reduced | zero | exempt
        'is_compound',    // applied on top of other taxes
        'is_active',
        'applies_to',     // all | physical | digital | shipping
    ];

    protected $casts = [
        'rate'        => 'decimal:4',
        'is_compound' => 'boolean',
        'is_active'   => 'boolean',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function zone(): BelongsTo
    {
        return $this->belongsTo(TaxZone::class, 'tax_zone_id');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
