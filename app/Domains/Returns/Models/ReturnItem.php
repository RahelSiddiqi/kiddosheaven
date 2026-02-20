<?php

namespace App\Domains\Returns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    protected $fillable = [
        'return_request_id',
        'order_item_id',
        'quantity',
        'condition',
        'reason',
        'notes',
        'refund_amount',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
    ];

    const CONDITIONS = [
        'new'       => 'Unopened / New',
        'like_new'  => 'Like New',
        'good'      => 'Good',
        'fair'      => 'Fair',
        'poor'      => 'Poor',
        'damaged'   => 'Damaged',
    ];

    const REASONS = [
        'defective'       => 'Defective / Damaged',
        'wrong_item'      => 'Wrong Item Sent',
        'changed_mind'    => 'Changed My Mind',
        'not_as_expected' => 'Not As Expected',
        'size_issue'      => 'Wrong Size / Fit',
        'other'           => 'Other',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(\App\Domains\Order\Models\OrderItem::class);
    }
}
