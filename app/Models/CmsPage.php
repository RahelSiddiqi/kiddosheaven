<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_active',
        'template',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the page by slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }

    /**
     * Generate URL friendly slug from title
     */
    public function generateSlug(): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->title)));
    }

    /**
     * Get pages grouped by template
     */
    public static function getByTemplate(string $template)
    {
        return static::where('template', $template)->where('is_active', true)->get();
    }
}
