<?php

namespace App\Domains\Concerns;

use App\Domains\Catalog\Models\Tag;

/**
 * Add polymorphic tag support to any Eloquent model.
 * Usage: use HasTags; on Product, Order, User, etc.
 */
trait HasTags
{
    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Sync tags by name array. Creates new tags if they don't exist.
     */
    public function syncTagNames(array $names, string $type = 'general'): void
    {
        $siteId = $this->site_id ?? optional(app('current.site'))->id ?? null;

        $tagIds = collect($names)
            ->filter()
            ->map(fn ($name) => Tag::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name), 'site_id' => $siteId],
                ['name' => $name, 'type' => $type, 'site_id' => $siteId]
            )->id)
            ->values()
            ->all();

        $this->tags()->sync($tagIds);
    }

    /**
     * Attach tags by name, non-destructive (additive).
     */
    public function attachTagNames(array $names, string $type = 'general'): void
    {
        $siteId = $this->site_id ?? optional(app('current.site'))->id ?? null;

        $tagIds = collect($names)
            ->filter()
            ->map(fn ($name) => Tag::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name), 'site_id' => $siteId],
                ['name' => $name, 'type' => $type, 'site_id' => $siteId]
            )->id)
            ->values()
            ->all();

        $this->tags()->syncWithoutDetaching($tagIds);
    }

    /**
     * Get tag names as a comma-separated string.
     */
    public function tagList(): string
    {
        return $this->tags->pluck('name')->join(', ');
    }
}
