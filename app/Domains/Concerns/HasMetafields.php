<?php

namespace App\Domains\Concerns;

use App\Domains\Metafield\Models\Metafield;

/**
 * Add Shopify-style metafield support to any Eloquent model.
 */
trait HasMetafields
{
    public function metafields(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Metafield::class, 'owner');
    }

    /**
     * Get a metafield value by namespace + key.
     */
    public function meta(string $namespace, string $key, mixed $default = null): mixed
    {
        $field = $this->metafields()
            ->where('namespace', $namespace)
            ->where('key', $key)
            ->first();

        return $field ? $field->typedValue() : $default;
    }

    /**
     * Set (upsert) a metafield.
     */
    public function setMeta(string $namespace, string $key, mixed $value, string $type = 'string'): Metafield
    {
        $encoded = is_array($value) || is_object($value)
            ? json_encode($value)
            : (string) $value;

        return $this->metafields()->updateOrCreate(
            ['namespace' => $namespace, 'key' => $key],
            [
                'value'      => $encoded,
                'value_type' => is_array($value) ? 'json' : $type,
                'site_id'    => $this->site_id ?? optional(app('current.site'))->id,
            ]
        );
    }

    /**
     * Get all metafields for a namespace as key=>typed_value array.
     */
    public function metaNamespace(string $namespace): array
    {
        return $this->metafields()
            ->where('namespace', $namespace)
            ->get()
            ->mapWithKeys(fn ($f) => [$f->key => $f->typedValue()])
            ->all();
    }
}
