<?php

namespace App\Domains\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Concerns\BelongsToSite;

class Address extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'user_id',
        'type',
        'name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'district',
        'postal_code',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
