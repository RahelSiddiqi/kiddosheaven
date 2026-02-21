<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CapitalAccountController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\FinancialTransactionController;
use App\Http\Controllers\Admin\InventoryMovementController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\LoyaltyController;
use App\Http\Controllers\Admin\Attribute\AttributeController;
use App\Http\Controllers\Admin\Attribute\AttributeValueController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Admin\Product\ProductImageController;
use App\Http\Controllers\Admin\Product\ProductVariantController;
use App\Http\Controllers\Admin\Product\ProductAttributeValueController;
use App\Http\Controllers\Admin\PurchaseBatchController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PartnerCalculationController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\InvestmentController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

// Admin Authentication Routes (outside middleware)
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->middleware('guest')->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'adminLogin'])->middleware('guest')->name('admin.login.submit');

Route::post('/admin/logout', [LoginController::class, 'adminLogout'])->name('admin.logout');

// Protected Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard — Livewire full-page component
    Route::get('/', \App\Livewire\Admin\Dashboard\Dashboard::class)->middleware('permission:view-dashboard')->name('dashboard');

    // ============================================
    // CATEGORIES
    // ============================================
    Route::prefix('categories')->name('categories.')->middleware('permission:view-categories')->group(function () {
        Route::get('/', \App\Livewire\Admin\Catalog\CategoryIndex::class)->name('index');
        // CategoryIndex inline modal handles create / edit / delete directly via Eloquent
    });

    // ============================================
    // PRICING TEMPLATES
    // ============================================
    Route::prefix('pricing-templates')->name('pricing-templates.')->middleware('permission:view-categories')->group(function () {
        Route::get('/', \App\Livewire\Admin\Catalog\PricingTemplateIndex::class)->name('index');
        // PricingTemplateIndex handles all CRUD inline
    });

    // ============================================
    // ATTRIBUTES (Global)
    // ============================================
    Route::prefix('attributes')->name('attributes.')->middleware('permission:view-categories')->group(function () {
        // Livewire index and value manager
        Route::get('/', \App\Livewire\Admin\Product\AttributeIndex::class)->name('index');
        Route::get('/{attributeId}/values', \App\Livewire\Admin\Product\AttributeValueManager::class)->name('values.index');
        // AttributeIndex and AttributeValueManager handle all CRUD inline
    });

    // ============================================
    // PRODUCTS
    // ============================================
    Route::prefix('products')->name('products.')->middleware('permission:view-products')->group(function () {
        // Bulk action route
        Route::post('bulk-action', [ProductController::class, 'bulkAction'])->name('bulk-action');

        // Get attributes by category (AJAX helper)
        Route::get('attributes/{category}', [ProductController::class, 'getAttributesByCategory'])->name('attributes');

        // Simplified product creation (NEW)
        Route::get('create/simple', function () {
            $categories = \App\Services\CategoryService::class;
            $categories = app($categories)->getHierarchicalList();
            $brands = \App\Models\Brand::orderBy('name')->get();
            return view('admin.products.create-simple', compact('categories', 'brands'));
        })->name('create.simple');

        // Get variant attributes by category (AJAX helper)
        Route::get('variant-attributes/{category}', [ProductController::class, 'getVariantAttributes'])->name('variant-attributes');

        // Get non-variant attributes by category (AJAX helper)
        Route::get('non-variant-attributes/{category}', [ProductController::class, 'getNonVariantAttributes'])->name('non-variant-attributes');

        // Product-specific nested routes
        Route::prefix('{product}')->group(function () {
            // Image management
            Route::post('images/upload', [ProductImageController::class, 'upload'])->name('images.upload');
            Route::post('images/set-primary', [ProductImageController::class, 'setPrimary'])->name('images.set-primary');
            Route::delete('images/delete', [ProductImageController::class, 'destroy'])->name('images.destroy');
            Route::post('images/reorder', [ProductImageController::class, 'reorder'])->name('images.reorder');

            // Variant management
            Route::get('variants', [ProductVariantController::class, 'index'])->name('variants.index');
            Route::get('variants/{variant}', [ProductVariantController::class, 'show'])->name('variants.show');
            Route::post('variants', [ProductVariantController::class, 'store'])->name('variants.store');
            Route::put('variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
            Route::delete('variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
            Route::post('variants/generate', [ProductVariantController::class, 'generate'])->name('variants.generate');
            Route::post('variants/bulk-update', [ProductVariantController::class, 'bulkUpdate'])->name('variants.bulk-update');

            // Attribute value management
            Route::get('attribute-values', [ProductAttributeValueController::class, 'index'])->name('attribute-values.index');
            Route::post('attribute-values', [ProductAttributeValueController::class, 'store'])->name('attribute-values.store');
            Route::put('attribute-values', [ProductAttributeValueController::class, 'update'])->name('attribute-values.update');
            Route::delete('attribute-values/{attribute}', [ProductAttributeValueController::class, 'destroy'])->name('attribute-values.destroy');
            Route::post('attribute-values/sync-from-category', [ProductAttributeValueController::class, 'syncFromCategory'])->name('attribute-values.sync');
        });

        // Livewire handles product index, create and edit; ProductForm calls ProductService directly
        Route::get('/', \App\Livewire\Admin\Product\ProductIndex::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Product\ProductForm::class)->name('create');
        Route::get('/{productId}/edit', \App\Livewire\Admin\Product\ProductForm::class)->name('edit');
        Route::resource('/', ProductController::class)->parameters(['' => 'product'])->except(['index', 'create', 'edit', 'store', 'update']);
    });

    // ============================================
    // BRANDS
    // ============================================
    Route::prefix('brands')->name('brands.')->middleware('permission:view-brands')->group(function () {
        Route::get('/', \App\Livewire\Admin\Catalog\BrandIndex::class)->name('index');
        // BrandIndex inline modal handles create / edit / delete / toggle directly via Eloquent
    });

    // ============================================
    // ORDERS
    // ============================================
    Route::prefix('orders')->name('orders.')->middleware('permission:view-orders')->group(function () {
        // Export and invoice routes (return downloadable content)
        Route::get('export', [OrderController::class, 'export'])->name('export');
        Route::get('{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');

        // Livewire handles order index and show
        Route::get('/', \App\Livewire\Admin\Order\OrderIndex::class)->name('index');
        Route::get('/{orderId}', \App\Livewire\Admin\Order\OrderShow::class)->name('show');
    });

    // ============================================
    // INVENTORY
    // ============================================
    Route::prefix('inventory')->name('inventory.')->middleware('permission:view-inventory')->group(function () {
        // Specific routes before generic index
        Route::get('alerts', \App\Livewire\Admin\Inventory\InventoryAlerts::class)->name('alerts');
        Route::post('update', [InventoryController::class, 'updateStock'])->name('update');
        Route::get('/', \App\Livewire\Admin\Inventory\InventoryIndex::class)->name('index');

        // Inventory Movements (nested under inventory) - Livewire handles all CRUD inline
        Route::prefix('movements')->name('movements.')->group(function () {
            Route::get('/', \App\Livewire\Admin\Inventory\InventoryMovementIndex::class)->name('index');
            // InventoryMovementIndex inline modal handles create/edit/delete
        });
    });

    // Purchase Batches - Livewire handles all CRUD inline
    Route::prefix('purchase-batches')->name('purchase-batches.')->middleware('permission:view-inventory')->group(function () {
        Route::get('/', \App\Livewire\Admin\Inventory\PurchaseBatchIndex::class)->name('index');
        // PurchaseBatchIndex inline modal handles create/edit/delete/mark-exhausted
    });

    // ============================================
    // CUSTOMERS
    // ============================================
    Route::prefix('customers')->name('customers.')->middleware('permission:view-customers')->group(function () {
        // Livewire handles customer index and show
        Route::get('/', \App\Livewire\Admin\Customer\CustomerIndex::class)->name('index');
        Route::get('/segments', \App\Livewire\Admin\Customer\CustomerSegments::class)->name('segments.index');
        Route::get('/{customerId}', \App\Livewire\Admin\Customer\CustomerShow::class)->name('show');
        // CustomerIndex and CustomerShow handle toggle directly via Eloquent
    });

    // ============================================
    // MARKETING
    // ============================================
    Route::prefix('marketing')->name('marketing.')->middleware('permission:view-marketing')->group(function () {
        // Coupons
        Route::prefix('coupons')->name('coupons.')->group(function () {
            // Specific routes
            Route::get('users', [CouponController::class, 'getUsers'])->name('users');

            // Livewire handles coupon index and form
            Route::get('/', \App\Livewire\Admin\Marketing\CouponIndex::class)->name('index');
            Route::get('/create', \App\Livewire\Admin\Marketing\CouponForm::class)->name('create');
            Route::get('/{couponId}/edit', \App\Livewire\Admin\Marketing\CouponForm::class)->name('edit');

            // Only keep destroy - CouponForm handles store/update directly
            Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
        });

        // Flash Sales
        Route::prefix('flash-sales')->name('flash-sales.')->group(function () {
            // Livewire handles flash sale index and form
            Route::get('/', \App\Livewire\Admin\Marketing\FlashSaleIndex::class)->name('index');
            Route::get('/create', \App\Livewire\Admin\Marketing\FlashSaleForm::class)->name('create');
            Route::get('/{flashSaleId}/edit', \App\Livewire\Admin\Marketing\FlashSaleForm::class)->name('edit');

            // Only keep destroy - FlashSaleForm handles store/update directly
            Route::delete('/{flashSale}', [FlashSaleController::class, 'destroy'])->name('destroy');
        });
    });

    // ============================================
    // CMS
    // ============================================
    Route::prefix('cms')->name('cms.')->middleware('permission:view-cms')->group(function () {
        Route::prefix('pages')->name('pages.')->group(function () {
            // Livewire handles CMS page index and form
            Route::get('/', \App\Livewire\Admin\Cms\CmsPageIndex::class)->name('index');
            Route::get('/create', \App\Livewire\Admin\Cms\CmsPageForm::class)->name('create');
            Route::get('/{pageId}/edit', \App\Livewire\Admin\Cms\CmsPageForm::class)->name('edit');

            // Only keep destroy - CmsPageForm handles store/update directly
            Route::delete('/{page}', [CmsPageController::class, 'destroy'])->name('destroy');
        });
    });

    // ============================================
    // REVIEWS
    // ============================================
    Route::prefix('reviews')->name('reviews.')->middleware('permission:view-reviews')->group(function () {
        // Livewire handles review index with approve, reject, delete, bulkApprove, bulkDelete
        Route::get('/', \App\Livewire\Admin\Reviews\ReviewIndex::class)->name('index');

        // Resource routes (only show for viewing individual review)
        Route::get('/{review}', [ReviewController::class, 'show'])->name('show');
    });

    // ============================================
    // CAPITAL ACCOUNTS
    // ============================================
    Route::prefix('capital-accounts')->name('capital-accounts.')->middleware('permission:view-finance')->group(function () {
        Route::get('/', \App\Livewire\Admin\Finance\CapitalAccountIndex::class)->name('index');
        // CapitalAccountIndex handles create/edit/delete inline
    });

    // ============================================
    // FINANCIAL TRANSACTIONS
    // ============================================
    Route::prefix('financial-transactions')->name('financial-transactions.')->middleware('permission:view-finance')->group(function () {
        Route::get('/', \App\Livewire\Admin\Finance\FinancialTransactionIndex::class)->name('index');
        // FinancialTransactionIndex handles create/edit/delete inline
    });

    // ============================================
    // EXPENSES (Accounting)
    // ============================================
    Route::prefix('expenses')->name('expenses.')->middleware('permission:view-finance')->group(function () {
        Route::get('/', \App\Livewire\Admin\Accounting\AccountingOverview::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Accounting\ExpenseForm::class)->name('create');
        Route::get('/{expenseId}/edit', \App\Livewire\Admin\Accounting\ExpenseForm::class)->name('edit');
        // AccountingOverview handles approve/reject/delete, ExpenseForm handles create/update
    });

    // ============================================
    // PARTNERS
    // ============================================
    Route::prefix('partners')->name('partners.')->middleware('permission:view-finance')->group(function () {
        Route::get('/', \App\Livewire\Admin\Finance\PartnerIndex::class)->name('index');
        // PartnerIndex handles all partner CRUD, payments, and commission calculations inline
    });

    // ============================================
    // INVESTMENTS
    // ============================================
    Route::prefix('investments')->name('investments.')->middleware('permission:view-finance')->group(function () {
        Route::get('/', \App\Livewire\Admin\Finance\InvestmentIndex::class)->name('index');
        // InvestmentIndex handles create/edit/delete/status inline
    });

    // ============================================
    // REPORTS
    // ============================================
    Route::prefix('reports')->name('reports.')->middleware('permission:view-reports')->group(function () {
        Route::get('/', \App\Livewire\Admin\Reports\ReportsDashboard::class)->name('index');

        // Inventory Reports
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('batch-stock', [ReportController::class, 'batchStock'])->name('batch-stock');
        Route::get('batch-stock/export', [ReportController::class, 'exportBatchStock'])->name('batch-stock-export');
        Route::get('expiring', [ReportController::class, 'expiringItems'])->name('expiring');

        // Sales Reports
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('products', [ReportController::class, 'products'])->name('products');
        Route::get('product-profit', [ReportController::class, 'productProfit'])->name('product-profit');
        Route::get('category-profit', [ReportController::class, 'categoryProfit'])->name('category-profit');

        // Partner Reports
        Route::get('partners', [ReportController::class, 'partners'])->name('partners');
        Route::get('partner-contribution', [ReportController::class, 'partnerContribution'])->name('partner-contribution');
        Route::get('investor-roi', [ReportController::class, 'investorROI'])->name('investor-roi');

        // Financial Reports
        Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('investments', [ReportController::class, 'investments'])->name('investments');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('financial-summary', [ReportController::class, 'financialSummary'])->name('financial-summary');

        // Audit Reports
        Route::get('cost-history', [ReportController::class, 'costHistory'])->name('cost-history');
    });

    // ============================================
    // SETTINGS & ADMINISTRATION
    // ============================================

    // Users
    Route::prefix('users')->name('users.')->middleware('permission:view-users')->group(function () {
        Route::get('/', \App\Livewire\Admin\User\UserIndex::class)->middleware('permission:view-users')->name('index');
        Route::get('/create', \App\Livewire\Admin\User\UserForm::class)->middleware('permission:create-users')->name('create');
        Route::get('/{userId}/edit', \App\Livewire\Admin\User\UserForm::class)->middleware('permission:manage-users')->name('edit');
        // UserForm handles create/update directly, only keep destroy
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->middleware('permission:manage-users')->name('destroy');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', \App\Livewire\Admin\Profile\ProfileSettings::class)->name('index');
        // ProfileSettings handles update + password via Livewire
    });

    Route::prefix('settings')->name('settings.')->middleware('permission:view-settings')->group(function () {
        Route::get('/', \App\Livewire\Admin\Settings\ApplicationSettings::class)->name('edit');
        // ApplicationSettings handles save() via Livewire
    });

    // Theme Settings
    Route::prefix('theme')->name('theme.')->middleware('permission:view-settings')->group(function () {
        Route::get('/', \App\Livewire\Admin\Theme\ThemeSettings::class)->name('index');
    });

    // ============================================
    // SAAS ADMIN
    // ============================================
    Route::prefix('sites')->name('sites.')->middleware('permission:view-settings')->group(function () {
        Route::get('/', \App\Livewire\Admin\Site\SiteIndex::class)->name('index');
    });

    // Roles & Permissions (Livewire)
    Route::prefix('roles')->name('roles.')->middleware('permission:view-roles')->group(function () {
        Route::get('/', \App\Livewire\Admin\Role\RoleIndex::class)->name('index');
        // RoleIndex inline modal handles create / edit / delete with permission assignment
    });

    Route::prefix('permissions')->name('permissions.')->middleware('permission:view-roles')->group(function () {
        Route::get('/', \App\Livewire\Admin\Permission\PermissionIndex::class)->name('index');
        // PermissionIndex handles create / edit / delete / assign / generate
    });

    // ============================================
    // LOYALTY PROGRAM
    // ============================================
    Route::prefix('loyalty')->name('loyalty.')->middleware('permission:view-marketing')->group(function () {
        Route::get('/', \App\Livewire\Admin\Loyalty\LoyaltyManagement::class)->name('index');
        // LoyaltyManagement handles saveSettings() and adjustPoints() directly via Eloquent
    });

    // ============================================
    // SHIPPING
    // ============================================
    Route::prefix('shipping')->name('shipping.')->middleware('permission:view-orders')->group(function () {
        Route::prefix('zones')->name('zones.')->group(function () {
            Route::get('/', \App\Livewire\Admin\Shipping\ShippingZoneIndex::class)->name('index');
        });
        Route::prefix('shipments')->name('shipments.')->group(function () {
            Route::get('/', \App\Livewire\Admin\Shipping\ShipmentIndex::class)->name('index');
        });
    });

    // ============================================
    // RETURNS & REFUNDS
    // ============================================
    Route::prefix('returns')->name('returns.')->middleware('permission:view-orders')->group(function () {
        Route::get('/', \App\Livewire\Admin\Returns\ReturnIndex::class)->name('index');
    });

    // ============================================
    // COLLECTIONS
    // ============================================
    Route::prefix('collections')->name('collections.')->middleware('permission:view-catalog')->group(function () {
        Route::get('/', \App\Livewire\Admin\Catalog\CollectionIndex::class)->name('index');
        Route::get('/create', \App\Livewire\Admin\Catalog\CollectionForm::class)->name('create');
        Route::get('/{id}/edit', \App\Livewire\Admin\Catalog\CollectionForm::class)->name('edit');
    });

    // ============================================
    // PAYMENT GATEWAYS
    // ============================================
    Route::prefix('payment-gateways')->name('payment-gateways.')->middleware('permission:view-settings')->group(function () {
        Route::get('/', \App\Livewire\Admin\Payment\PaymentGatewayIndex::class)->name('index');
    });

    // ============================================
    // TAX ZONES
    // ============================================
    Route::prefix('tax-zones')->name('tax-zones.')->middleware('permission:view-settings')->group(function () {
        Route::get('/', \App\Livewire\Admin\Settings\TaxZoneIndex::class)->name('index');
    });

    // ============================================
    // URL REDIRECTS
    // ============================================
    Route::prefix('redirects')->name('redirects.')->middleware('permission:view-settings')->group(function () {
        Route::get('/', \App\Livewire\Admin\Settings\RedirectIndex::class)->name('index');
    });

    // ============================================
    // TAGS
    // ============================================
    Route::prefix('tags')->name('tags.')->middleware('permission:view-catalog')->group(function () {
        Route::get('/', \App\Livewire\Admin\Catalog\TagIndex::class)->name('index');
    });

    // ============================================
    // B2B PRICE LISTS
    // ============================================
    Route::prefix('b2b')->name('b2b.')->middleware('permission:view-customers')->group(function () {
        Route::get('/price-lists', \App\Livewire\Admin\Customer\B2BPriceListManager::class)->name('price-lists');
    });

    // ============================================
    // ABANDONED CARTS
    // ============================================
    Route::prefix('abandoned-carts')->name('abandoned-carts.')->middleware('permission:view-marketing')->group(function () {
        Route::get('/', \App\Livewire\Admin\Marketing\AbandonedCartIndex::class)->name('index');
    });

    // ============================================
    // DRAFT ORDERS
    // ============================================
    Route::prefix('draft-orders')->name('draft-orders.')->middleware('permission:view-orders')->group(function () {
        Route::get('/', \App\Livewire\Admin\Order\DraftOrderIndex::class)->name('index');
    });
});
