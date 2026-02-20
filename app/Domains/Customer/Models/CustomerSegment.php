<?php

namespace App\Domains\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\Concerns\BelongsToSite;

class CustomerSegment extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'name',
        'description',
        'conditions',
        'condition_match',
        'customer_count',
        'is_active',
    ];

    protected $casts = [
        'conditions'     => 'array',
        'is_active'      => 'boolean',
        'customer_count' => 'integer',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'customer_segment_users'
        )->withPivot('joined_at');
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    public function refreshCount(): void
    {
        $this->update(['customer_count' => $this->users()->count()]);
    }
}
