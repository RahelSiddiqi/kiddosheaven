<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes here are prefixed with /api and use the 'api' middleware group.
| Authentication is done via API key (X-API-Key header).
*/

// Public endpoints (no auth required)
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
})->name('api.health');

// Authenticated API endpoints
Route::middleware(['api.key'])->group(function () {

    // -- Products --
    Route::prefix('products')->name('api.products.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProductController::class, 'index'])->name('index');
        Route::get('{slug}', [\App\Http\Controllers\Api\ProductController::class, 'show'])->name('show');
    });

    // -- Categories --
    Route::prefix('categories')->name('api.categories.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\CategoryController::class, 'index'])->name('index');
        Route::get('{slug}', [\App\Http\Controllers\Api\CategoryController::class, 'show'])->name('show');
    });

    // -- Orders --
    Route::middleware(['api.key:orders'])->prefix('orders')->name('api.orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\OrderController::class, 'index'])->name('index');
        Route::get('{orderNumber}', [\App\Http\Controllers\Api\OrderController::class, 'show'])->name('show');
    });

    // -- Webhooks --
    Route::middleware(['api.key:webhooks'])->prefix('webhooks')->name('api.webhooks.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\WebhookController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Api\WebhookController::class, 'store'])->name('store');
        Route::delete('{id}', [\App\Http\Controllers\Api\WebhookController::class, 'destroy'])->name('destroy');
    });

    // -- Site Info --
    Route::get('/site', [\App\Http\Controllers\Api\SiteController::class, 'show'])->name('api.site.show');
});
