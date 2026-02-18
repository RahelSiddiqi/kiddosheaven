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
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PurchaseBatchController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
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
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->middleware('permission:view-dashboard')->name('dashboard');

    // ============================================
    // CATEGORIES
    // ============================================
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::post('reorder', [\App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->middleware('permission:manage-categories')->name('reorder');
        Route::resource('/', \App\Http\Controllers\Admin\CategoryController::class)->parameters(['' => 'category']);
    })->middleware('permission:view-categories');

    // ============================================
    // PRICING TEMPLATES
    // ============================================
    Route::prefix('pricing-templates')->name('pricing-templates.')->group(function () {
        Route::get('/list', [\App\Http\Controllers\Admin\PricingTemplateController::class, 'list'])->name('list');
        Route::post('/preview', [\App\Http\Controllers\Admin\PricingTemplateController::class, 'preview'])->name('preview');
        Route::post('{pricingTemplate}/attach-categories', [\App\Http\Controllers\Admin\PricingTemplateController::class, 'attachCategories'])->name('attach-categories');
        Route::resource('/', \App\Http\Controllers\Admin\PricingTemplateController::class)->parameters(['' => 'pricingTemplate']);
    })->middleware('permission:view-categories');

    // ============================================
    // ATTRIBUTES (Global)
    // ============================================
    Route::prefix('attributes')->name('attributes.')->middleware('permission:view-categories')->group(function () {
        // Action routes before resource
        Route::post('reorder', [AttributeController::class, 'reorder'])->name('reorder');

        // Attribute Values (nested under specific attribute)
        Route::prefix('{attribute}/values')->name('values.')->group(function () {
            Route::get('edit', [AttributeValueController::class, 'edit'])->name('edit');
            Route::post('/', [AttributeValueController::class, 'store'])->name('store');
            Route::post('bulk', [AttributeValueController::class, 'storeBulk'])->name('store-bulk');
            Route::put('{value}', [AttributeValueController::class, 'update'])->name('update');
            Route::delete('{value}', [AttributeValueController::class, 'destroy'])->name('destroy');
            Route::post('reorder', [AttributeValueController::class, 'reorder'])->name('reorder');
        });

        // Resource routes last
        Route::resource('/', AttributeController::class)->parameters(['' => 'attribute']);
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

        // Resource routes last
        Route::resource('/', ProductController::class)->parameters(['' => 'product']);
    });

    // ============================================
    // BRANDS
    // ============================================
    Route::prefix('brands')->name('brands.')->middleware('permission:view-brands')->group(function () {
        // Action routes before resource
        Route::post('{brand}/toggle', [BrandController::class, 'toggleActive'])->middleware('permission:manage-brands')->name('toggle');

        // Resource routes
        Route::resource('/', BrandController::class)->parameters(['' => 'brand']);
    });

    // ============================================
    // ORDERS
    // ============================================
    Route::prefix('orders')->name('orders.')->middleware('permission:view-orders')->group(function () {
        // Collection action routes before resource
        Route::post('bulk-status', [OrderController::class, 'bulkUpdateStatus'])->name('bulk-status');
        Route::get('export', [OrderController::class, 'export'])->name('export');

        // Single order action routes (with {order} parameter)
        Route::patch('{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::get('{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
        Route::post('{order}/ship', [OrderController::class, 'ship'])->name('ship');

        // Resource routes last (only index and show)
        Route::resource('/', OrderController::class)->parameters(['' => 'order'])->only(['index', 'show']);
    });

    // ============================================
    // INVENTORY
    // ============================================
    Route::prefix('inventory')->name('inventory.')->middleware('permission:view-inventory')->group(function () {
        // Specific routes before generic index
        Route::get('alerts', [InventoryController::class, 'alerts'])->name('alerts');
        Route::post('update', [InventoryController::class, 'updateStock'])->name('update');
        Route::get('/', [InventoryController::class, 'index'])->name('index');

        // Inventory Movements (nested under inventory)
        Route::prefix('movements')->name('movements.')->group(function () {
            // Specific action routes
            Route::get('product/{product}', [InventoryMovementController::class, 'getByProduct'])->name('by-product');

            // Resource routes
            Route::resource('/', InventoryMovementController::class)->parameters(['' => 'inventory_movement']);
        });
    });

    // Purchase Batches
    Route::prefix('purchase-batches')->name('purchase-batches.')->middleware('permission:view-inventory')->group(function () {
        // Specific action routes
        Route::get('product/{product}', [PurchaseBatchController::class, 'getByProduct'])->name('by-product');
        Route::patch('{purchaseBatch}/mark-exhausted', [PurchaseBatchController::class, 'markExhausted'])->name('mark-exhausted');

        // Resource routes
        Route::resource('/', PurchaseBatchController::class)->parameters(['' => 'purchase_batch']);
    });

    // ============================================
    // CUSTOMERS
    // ============================================
    Route::prefix('customers')->name('customers.')->middleware('permission:view-customers')->group(function () {
        // Action routes
        Route::post('{customer}/toggle', [CustomerController::class, 'toggleActive'])->name('toggle');

        // Resource routes (only index and show)
        Route::resource('/', CustomerController::class)->parameters(['' => 'customer'])->only(['index', 'show']);
    });

    // ============================================
    // MARKETING
    // ============================================
    Route::prefix('marketing')->name('marketing.')->middleware('permission:view-marketing')->group(function () {
        // Coupons
        Route::prefix('coupons')->name('coupons.')->group(function () {
            // Specific routes
            Route::get('users', [CouponController::class, 'getUsers'])->name('users');

            // Resource routes
            Route::resource('/', CouponController::class)->parameters(['' => 'coupon']);
        });

        // Flash Sales
        Route::prefix('flash-sales')->name('flash-sales.')->group(function () {
            // Action routes
            Route::post('{flashSale}/toggle', [FlashSaleController::class, 'toggleStatus'])->name('toggle');

            // Resource routes
            Route::resource('/', FlashSaleController::class)->parameters(['' => 'flash_sale']);
        });
    });

    // ============================================
    // CMS
    // ============================================
    Route::prefix('cms')->name('cms.')->middleware('permission:view-cms')->group(function () {
        Route::resource('pages', CmsPageController::class)->parameters(['pages' => 'cms_page']);
    });

    // ============================================
    // REVIEWS
    // ============================================
    Route::prefix('reviews')->name('reviews.')->middleware('permission:view-reviews')->group(function () {
        // Bulk action routes
        Route::post('bulk-approve', [ReviewController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('bulk-destroy', [ReviewController::class, 'bulkDestroy'])->name('bulk-destroy');

        // Single review actions
        Route::post('{review}/approve', [ReviewController::class, 'approve'])->name('approve');

        // Resource routes (only index, show, destroy)
        Route::resource('/', ReviewController::class)->parameters(['' => 'review'])->only(['index', 'show', 'destroy']);
    });

    // ============================================
    // CAPITAL ACCOUNTS
    // ============================================
    Route::prefix('capital-accounts')->name('capital-accounts.')->middleware('permission:view-finance')->group(function () {
        // Specific routes
        Route::get('partner/{partner}', [CapitalAccountController::class, 'getByPartner'])->name('by-partner');

        // Resource routes
        Route::resource('/', CapitalAccountController::class)->parameters(['' => 'capital_account']);
    });

    // ============================================
    // FINANCIAL TRANSACTIONS
    // ============================================
    Route::prefix('financial-transactions')->name('financial-transactions.')->middleware('permission:view-finance')->group(function () {
        // Specific routes
        Route::get('account/{account}', [FinancialTransactionController::class, 'getByAccount'])->name('by-account');

        // Resource routes
        Route::resource('/', FinancialTransactionController::class)->parameters(['' => 'financial_transaction']);
    });

    // ============================================
    // EXPENSES
    // ============================================
    Route::prefix('expenses')->name('expenses.')->middleware('permission:view-finance')->group(function () {
        // Category routes
        Route::post('categories', [ExpenseController::class, 'storeCategory'])->name('store-category');
        Route::delete('categories/{category}', [ExpenseController::class, 'destroyCategory'])->name('destroy-category');

        // Single expense actions
        Route::post('{expense}/approve', [ExpenseController::class, 'approve'])->name('approve');
        Route::post('{expense}/reject', [ExpenseController::class, 'reject'])->name('reject');

        // Resource routes
        Route::resource('/', ExpenseController::class)->parameters(['' => 'expense']);
    });

    // ============================================
    // PARTNERS
    // ============================================
    Route::prefix('partners')->name('partners.')->middleware('permission:view-finance')->group(function () {
        // Partner Calculations (nested group with static "calculations" prefix)
        Route::prefix('calculations')->name('calculations.')->group(function () {
            Route::post('{calculation}/approve', [PartnerCalculationController::class, 'approve'])->name('approve');
            Route::post('{calculation}/mark-paid', [PartnerCalculationController::class, 'markPaid'])->name('mark-paid');
        });

        // Partner-specific actions (with {partner} parameter)
        Route::put('{partner}/status', [PartnerController::class, 'updateStatus'])->name('update-status');
        Route::post('{partner}/payments', [PartnerController::class, 'storePayment'])->name('payments.store');
        Route::put('{partner}/payments/{payment}', [PartnerController::class, 'updatePayment'])->name('payments.update');
        Route::delete('{partner}/payments/{payment}', [PartnerController::class, 'destroyPayment'])->name('payments.destroy');
        Route::post('{partner}/calculate', [PartnerController::class, 'calculateCommission'])->name('calculate');

        // Resource routes last
        Route::resource('/', PartnerController::class)->parameters(['' => 'partner']);
    });

    // ============================================
    // INVESTMENTS
    // ============================================
    Route::prefix('investments')->name('investments.')->middleware('permission:view-finance')->group(function () {
        // Action routes
        Route::put('{investment}/status', [InvestmentController::class, 'updateStatus'])->name('update-status');

        // Resource routes
        Route::resource('/', InvestmentController::class)->parameters(['' => 'investment']);
    });

    // ============================================
    // REPORTS
    // ============================================
    Route::prefix('reports')->name('reports.')->middleware('permission:view-reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');

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
        Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->middleware('permission:create-users')->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->middleware('permission:create-users')->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->middleware('permission:manage-users')->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->middleware('permission:manage-users')->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->middleware('permission:manage-users')->name('destroy');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    Route::prefix('settings')->name('settings.')->middleware('permission:view-settings')->group(function () {
        Route::get('/', [SettingController::class, 'edit'])->name('edit');
        Route::put('/', [SettingController::class, 'update'])->middleware('permission:manage-settings')->name('update');
    });

    // Roles & Permissions
    Route::prefix('roles')->name('roles.')->middleware('permission:view-roles')->group(function () {
        Route::resource('/', RoleController::class)->parameters(['' => 'role']);
    });

    Route::prefix('permissions')->name('permissions.')->middleware('permission:view-roles')->group(function () {
        Route::resource('/', PermissionController::class)
            ->parameters(['' => 'permission'])
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('assign', [PermissionController::class, 'assign'])->name('assign');
        Route::post('generate', [PermissionController::class, 'generate'])->name('generate');
    });

    // ============================================
    // LOYALTY PROGRAM
    // ============================================
    Route::prefix('loyalty')->name('loyalty.')->middleware('permission:view-marketing')->group(function () {
        Route::get('/', [LoyaltyController::class, 'index'])->name('index');
        Route::put('{program}', [LoyaltyController::class, 'updateProgram'])->name('update');
        Route::post('add-points', [LoyaltyController::class, 'addPoints'])->name('add-points');
    });
});
