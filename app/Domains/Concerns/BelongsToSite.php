<?php

namespace App\Domains\Concerns;

use App\Domains\Site\Models\Site;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToSite
{
    public static function bootBelongsToSite(): void
    {
        // Auto-set site_id on create if tenant is resolved
        static::creating(function ($model) {
            if (empty($model->site_id) && app()->bound('current.site')) {
                $model->site_id = app('current.site')->id;
            }
        });

        // Global scope: only show records for the current site
        static::addGlobalScope('site', function (Builder $builder) {
            if (app()->bound('current.site')) {
                $builder->where($builder->getModel()->getTable() . '.site_id', app('current.site')->id);
            }
        });
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public static function withoutTenantScope(): Builder
    {
        return static::withoutGlobalScope('site');
    }
}
