<?php

namespace App\Domains\Metafield\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Domains\Concerns\BelongsToSite;

/**
 * Shopify-style polymorphic metafields.
 * Attach custom key-value data to any entity (product, order, customer, etc.)
 *
 * value_type: string | integer | float | boolean | json | date | url | color
 */
class Metafield extends Model
{
    use BelongsToSite;

    protected $fillable = [
        'site_id',
        'owner_type',   // Eloquent morph map key, e.g. "product", "order"
        'owner_id',
        'namespace',    // logical grouping: "specifications", "seo", "shipping"
        'key',
        'value',
        'value_type',   // string | integer | float | boolean | json | date | url | color
        'description',
    ];

    protected $casts = [
        'value_type' => 'string',
    ];

    // ---------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeNamespace($query, string $namespace)
    {
        return $query->where('namespace', $namespace);
    }

    public function scopeKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * Get typed value (cast value_type).
     */
    public function typedValue(): mixed
    {
        return match ($this->value_type) {
            'integer' => (int) $this->value,
            'float'   => (float) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($this->value, true),
            default   => $this->value,
        };
    }
}
