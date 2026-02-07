<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\LoyaltyController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\PartnerCalculationController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\InvestmentController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

// Admin Authentication Routes (outside middleware)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login.submit');
});

Route::post('/admin/logout', [LoginController::class, 'adminLogout'])->name('admin.logout');

// Protected Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Catalog Management
    Route::resource('catalogs', CatalogController::class);
    Route::post('catalogs/reorder', [CatalogController::class, 'reorder'])->name('catalogs.reorder');

    // Product Management
    Route::resource('products', ProductController::class);
    Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');
    Route::get('products/attributes/{catalog}', [ProductController::class, 'getAttributesByCatalog'])->name('products.attributes');

    // Product Attributes Management
    Route::resource('product-attributes', ProductAttributeController::class);
    Route::post('product-attributes/reorder', [ProductAttributeController::class, 'reorder'])->name('product-attributes.reorder');
    Route::delete('product-attributes/{attribute}/values/{value}', [ProductAttributeController::class, 'destroyValue'])->name('product-attributes.values.destroy');

    // Order Management
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('orders/bulk-status', [OrderController::class, 'bulkUpdateStatus'])->name('orders.bulkUpdateStatus');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::post('orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');

    // Brand Management
    Route::resource('brands', BrandController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('brands/{brand}/toggle', [BrandController::class, 'toggleActive'])->name('brands.toggle');

    // Inventory
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('inventory/alerts', [InventoryController::class, 'alerts'])->name('inventory.alerts');
    Route::post('inventory/update', [InventoryController::class, 'updateStock'])->name('inventory.update');

    // Customers
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);
    Route::post('customers/{customer}/toggle', [CustomerController::class, 'toggleActive'])->name('customers.toggle');

    // Coupons
    Route::resource('coupons', CouponController::class);
    Route::get('coupons/users', [CouponController::class, 'getUsers'])->name('coupons.users');

    // Flash Sales
    Route::resource('flash-sales', FlashSaleController::class);
    Route::post('flash-sales/{flashSale}/toggle', [FlashSaleController::class, 'toggleStatus'])->name('flash-sales.toggle');

    // CMS Pages
    Route::prefix('cms')->name('cms.')->group(function () {
        Route::resource('pages', CmsPageController::class);
    });

    // Reviews
    Route::resource('reviews', ReviewController::class)->only(['index', 'show', 'destroy']);
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/bulk-approve', [ReviewController::class, 'bulkApprove'])->name('reviews.bulkApprove');
    Route::post('reviews/bulk-destroy', [ReviewController::class, 'bulkDestroy'])->name('reviews.bulkDestroy');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('products', [ReportController::class, 'products'])->name('products');
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('partners', [ReportController::class, 'partners'])->name('partners');
        Route::get('investments', [ReportController::class, 'investments'])->name('investments');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profitLoss');
    });

    // Settings
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

    // Roles & Permissions
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    // Loyalty Program
    Route::get('loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::put('loyalty/{program}', [LoyaltyController::class, 'updateProgram'])->name('loyalty.updateProgram');
    Route::post('loyalty/add-points', [LoyaltyController::class, 'addPoints'])->name('loyalty.addPoints');

    // Product Attributes
    Route::resource('attributes', ProductAttributeController::class)->only(['index', 'store', 'update', 'destroy']);



    // Expense Management
    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('expenses.approve');
    Route::post('expenses/{expense}/reject', [ExpenseController::class, 'reject'])->name('expenses.reject');
    Route::post('expenses/categories', [ExpenseController::class, 'storeCategory'])->name('expenses.storeCategory');
    Route::delete('expenses/categories/{category}', [ExpenseController::class, 'destroyCategory'])->name('expenses.destroyCategory');

    // Partner Management
    Route::resource('partners', PartnerController::class);
    Route::put('partners/{partner}/status', [PartnerController::class, 'updateStatus'])->name('partners.update-status');
    Route::post('partners/{partner}/payments', [PartnerController::class, 'storePayment'])->name('partners.payments.store');
    Route::put('partners/{partner}/payments/{payment}', [PartnerController::class, 'updatePayment'])->name('partners.payments.update');
    Route::delete('partners/{partner}/payments/{payment}', [PartnerController::class, 'destroyPayment'])->name('partners.payments.destroy');
    Route::post('partners/{partner}/calculate', [PartnerController::class, 'calculateCommission'])->name('partners.calculate');
    Route::post('partners/calculations/{calculation}/approve', [PartnerCalculationController::class, 'approve'])->name('partners.calculations.approve');
    Route::post('partners/calculations/{calculation}/mark-paid', [PartnerCalculationController::class, 'markPaid'])->name('partners.calculations.markPaid');

    // Investment Tracking
    Route::resource('investments', InvestmentController::class);
    Route::put('investments/{investment}/status', [InvestmentController::class, 'updateStatus'])->name('investments.update-status');
});
