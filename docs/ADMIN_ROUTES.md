# Admin Routes Reference

## Route Organization Issue

### Problem

The route `admin/catalogs/types` returns 404 due to route order conflicts. Laravel matches routes in the order they are defined.

### Current Structure

```php
Route::prefix('catalogs')->name('catalogs.')->group(function () {
    Route::resource('', CatalogController::class); // Matches /catalogs/{catalog}
    // Other nested routes...

    Route::prefix('types')->name('types.')->group(function () {
        Route::resource('/', CatalogTypeController::class); // Should match /catalogs/types
        // But {catalog} resource catches it first!
    });
});
```

### Why It Fails

1. `Route::resource('', CatalogController::class)` creates route `/catalogs/{catalog}`
2. When accessing `/admin/catalogs/types`, Laravel matches `{catalog}` param first
3. It tries to find Catalog with ID "types", returns 404
4. Never reaches the `types` nested group

### Solution

**Specific routes BEFORE generic resource routes:**

```php
Route::prefix('catalogs')->name('catalogs.')->group(function () {
    // ✅ SPECIFIC ROUTES FIRST
    Route::prefix('types')->name('types.')->group(function () {
        Route::resource('/', CatalogTypeController::class);
    });

    // ✅ RESOURCE ROUTES LAST (with wildcards)
    Route::resource('', CatalogController::class);
});
```

## Correct Route Order Rules

### Priority Order (High to Low)

1. **Static paths** (`/admin/dashboard`)
2. **Nested specific paths** (`/admin/catalogs/types`)
3. **Action routes** (`/admin/products/bulk-action`)
4. **Named parameter routes** (`/admin/catalogs/{catalog}/attributes`)
5. **Resource routes** (`/admin/catalogs` with CRUD)

### Example Pattern

```php
Route::prefix('feature')->name('feature.')->group(function () {
    // 1. Static nested routes
    Route::get('special-page', [Controller::class, 'special']);

    // 2. Nested resource groups
    Route::prefix('{item}/relations')->name('relations.')->group(function () {
        Route::resource('/', RelationController::class);
    });

    // 3. Action routes on collection
    Route::post('bulk-action', [Controller::class, 'bulkAction']);

    // 4. Action routes on single item
    Route::post('{item}/toggle', [Controller::class, 'toggle']);

    // 5. Main resource LAST
    Route::resource('/', Controller::class);
});
```

## Current Admin Routes Map

```
GET    /admin                                    → admin.dashboard
GET    /admin/login                              → admin.login

# Catalogs
GET    /admin/catalogs                           → admin.catalogs.index
POST   /admin/catalogs                           → admin.catalogs.store
GET    /admin/catalogs/create                    → admin.catalogs.create
POST   /admin/catalogs/reorder                   → admin.catalogs.reorder
GET    /admin/catalogs/{catalog}                 → admin.catalogs.show
PUT    /admin/catalogs/{catalog}                 → admin.catalogs.update
DELETE /admin/catalogs/{catalog}                 → admin.catalogs.destroy
GET    /admin/catalogs/{catalog}/edit            → admin.catalogs.edit
PUT    /admin/catalogs/{catalog}/attributes      → admin.catalogs.update-attributes

# Catalog Attributes (nested)
GET    /admin/catalogs/{catalog}/attributes                    → admin.catalogs.attributes.index
POST   /admin/catalogs/{catalog}/attributes                    → admin.catalogs.attributes.attach
DELETE /admin/catalogs/{catalog}/attributes/{attribute}        → admin.catalogs.attributes.detach
PUT    /admin/catalogs/{catalog}/attributes/reorder            → admin.catalogs.attributes.reorder

# Catalog Types (should be /admin/catalogs/types but conflicts!)
GET    /admin/catalogs/types                     → NEEDS FIX - 404 ERROR
POST   /admin/catalogs/types                     → NEEDS FIX - 404 ERROR
...

# Products
GET    /admin/products                           → admin.products.index
POST   /admin/products                           → admin.products.store
GET    /admin/products/create                    → admin.products.create
POST   /admin/products/bulk-action               → admin.products.bulk-action
GET    /admin/products/attributes/{catalog}      → admin.products.attributes
GET    /admin/products/{product}                 → admin.products.show
PUT    /admin/products/{product}                 → admin.products.update
DELETE /admin/products/{product}                 → admin.products.destroy
GET    /admin/products/{product}/edit            → admin.products.edit

# Orders
GET    /admin/orders                             → admin.orders.index
GET    /admin/orders/{order}                     → admin.orders.show
PATCH  /admin/orders/{order}/status              → admin.orders.update-status
POST   /admin/orders/bulk-status                 → admin.orders.bulk-status
GET    /admin/orders/{order}/invoice             → admin.orders.invoice
GET    /admin/orders/export                      → admin.orders.export
POST   /admin/orders/{order}/ship                → admin.orders.ship

# (and so on for other features...)
```

## Route Testing Commands

```bash
# List all routes
php artisan route:list

# Filter by admin routes
php artisan route:list | grep admin

# Filter by specific feature
php artisan route:list | grep catalogs

# Check specific route
php artisan route:list --path=admin/catalogs/types
```
