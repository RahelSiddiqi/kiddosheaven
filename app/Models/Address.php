<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Domains\Concerns\BelongsToSite;

class Address extends Model
{
    use HasFactory;
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

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
