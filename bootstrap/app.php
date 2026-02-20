<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Prepend tenant resolution to web middleware group
        $middleware->prependToGroup('web', \App\Http\Middleware\ResolveTenant::class);

        $middleware->alias([
            'admin'        => \App\Http\Middleware\EnsureAdmin::class,
            'permission'   => \App\Http\Middleware\CheckPermission::class,
            'api.key'      => \App\Http\Middleware\AuthenticateApiKey::class,
            'plan.feature' => \App\Http\Middleware\CheckPlanFeature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
