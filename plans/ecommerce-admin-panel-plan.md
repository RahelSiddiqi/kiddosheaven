# E-Commerce Admin Panel Implementation Plan

## Project Overview

This plan outlines the comprehensive implementation of a professional e-commerce admin panel based on React Tailwind Admin design patterns. The project will feature dual layout modes and cover 8 major feature categories.

---

## 1. Layout Architecture

### 1.1 Two Layout Modes

#### Light Sidebar Version (Current → Enhanced)

- **Sidebar**: Dark blue/slate background (`#1e293b`)
- **Content Area**: White/light gray backgrounds
- **Header**: White with subtle border
- **Cards**: White with light borders
- **Primary Color**: Teal/Green (`hsl(178 99% 28 Dark Sidebar Version%)`)

#### (New)

- **Sidebar**: Dark blue/slate background (enhanced contrast)
- **Content Area**: Dark backgrounds (`#0f172a` or `#1e293b`)
- **Header**: Dark with subtle borders
- **Cards**: Dark with lighter borders
- **Text**: Light gray/white for readability
- **Primary Color**: Lighter teal for dark mode visibility

### 1.2 Layout File Structure

```
resources/views/admin/
├── layout.blade.php              # Light sidebar layout (enhanced)
├── layout-dark.blade.php         # NEW: Dark sidebar + dark content
└── components/
    ├── sidebar-light.blade.php   # Light mode sidebar
    ├── sidebar-dark.blade.php    # Dark mode sidebar
    ├── header-light.blade.php    # Light mode header
    ├── header-dark.blade.php     # Dark mode header
    └── theme-toggle.blade.php    # Theme switcher component
```

### 1.3 Theme Toggle Implementation

Add a theme toggle in the header that allows switching between light and dark modes. Store user preference in:

- Local storage for UI persistence
- User profile for permanent preference
- Default to system preference on first visit

```php
// In user migration
$table->string('admin_theme')->default('light')->after('is_admin');
```

---

## 2. Enhanced Dashboard Design

### 2.1 Dashboard Components

#### Stats Cards (8 key metrics)

```php
1. Total Revenue
2. Orders Today/This Week
3. Average Order Value
4. Customer Count
5. Conversion Rate
6. Returning Customers
7. Low Stock Alerts
8. Pending Orders
```

#### Charts Section

- **Sales Trend Chart**: Line chart showing revenue over time (7/30/90 days)
- **Orders by Status**: Doughnut chart (pending, processing, shipped, delivered)
- **Top Categories**: Bar chart of sales by product category
- **Customer Growth**: Area chart of new customer registrations

#### Recent Activity

- Recent orders with quick actions
- Low stock alerts
- Recent customer registrations
- Pending reviews/comments

#### Top Products Table

- Product name with thumbnail
- Units sold
- Revenue generated
- Stock status
- Quick edit link

#### Quick Actions Bar

- Add new product
- Create coupon
- View pending orders
- Export reports
- Manage inventory alerts

### 2.2 Dashboard File Structure

```
resources/views/admin/
├── dashboard.blade.php           # Light mode dashboard (enhanced)
└── dashboard-dark.blade.php      # NEW: Dark mode dashboard
    └── components/
        ├── stats-card.blade.php
        ├── sales-chart.blade.php
        ├── orders-chart.blade.php
        ├── recent-orders.blade.php
        ├── top-products.blade.php
        └── quick-actions.blade.php
```

### 2.3 Required JS Libraries

Add to `package.json`:

```json
{
    "chart.js": "^4.4.0",
    "chartjs-adapter-date-fns": "^3.0.0",
    "date-fns": "^3.0.0"
}
```

Or use a Laravel package like `laravel-apexcharts` for easy integration.

---

## 3. Database Schema Updates

### 3.1 New Tables Required

#### 1. `activity_logs` (Exists - Enhance)

```php
Schema::create('activity_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('action'); // created, updated, deleted, logged_in, etc.
    $table->string('model_type'); // App\Models\Product::class
    $table->unsignedBigInteger('model_id')->nullable();
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->string('ip_address')->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();

    $table->index(['model_type', 'model_id']);
    $table->index('user_id');
});
```

#### 2. `roles` & `permissions` (RBAC)

```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->timestamps();
});

Schema::create('permissions', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->string('module'); // products, orders, customers, etc.
    $table->text('description')->nullable();
    $table->timestamps();
});

Schema::create('role_permission', function (Blueprint $table) {
    $table->foreignId('role_id')->constrained()->onDelete('cascade');
    $table->foreignId('permission_id')->constrained()->onDelete('cascade');
    $table->primary(['role_id', 'permission_id']);
});

Schema::create('user_role', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('role_id')->constrained()->onDelete('cascade');
    $table->primary(['user_id', 'role_id']);
});
```

#### 3. `flash_sales`

```php
Schema::create('flash_sales', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('discount_percentage', 5, 2);
    $table->timestamp('starts_at');
    $table->timestamp('ends_at');
    $table->enum('status', ['scheduled', 'active', 'ended'])->default('scheduled');
    $table->timestamps();
});
```

#### 4. `flash_sale_products` (Pivot)

```php
Schema::create('flash_sale_products', function (Blueprint $table) {
    $table->foreignId('flash_sale_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->integer('discounted_quantity')->nullable(); // Stock allocated for flash sale
    $table->primary(['flash_sale_id', 'product_id']);
});
```

#### 5. `loyalty_points` (Per User)

```php
Schema::create('loyalty_points', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->integer('points')->default(0);
    $table->integer('lifetime_points')->default(0); // Total earned
    $table->integer('redeemed_points')->default(0);
    $table->timestamps();
});
```

#### 6. `loyalty_transactions`

```php
Schema::create('loyalty_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('order_id')->nullable()->constrained();
    $table->enum('type', ['earned', 'redeemed', 'expired', 'bonus']);
    $table->integer('points');
    $table->integer('balance_after');
    $table->text('description')->nullable();
    $table->timestamps();
});
```

#### 7. `product_views` (Analytics)

```php
Schema::create('product_views', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->string('session_id')->nullable();
    $table->foreignId('user_id')->nullable()->constrained();
    $table->string('ip_address')->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamp('viewed_at')->useCurrent();

    $table->index(['product_id', 'viewed_at']);
});
```

#### 8. `abandoned_carts`

```php
Schema::create('abandoned_carts', function (Blueprint $table) {
    $table->id();
    $table->string('session_id')->unique();
    $table->foreignId('user_id')->nullable()->constrained();
    $table->string('email')->nullable(); // For guest carts
    $table->decimal('subtotal', 10, 2);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    $table->json('items'); // Serialized cart items
    $table->timestamp('recovered_at')->nullable();
    $table->timestamps();

    $table->index('email');
    $table->index(['recovered_at', 'status']);
});
```

#### 9. `pages` (CMS)

```php
Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('content')->nullable();
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->timestamps();
});
```

#### 10. `blog_posts`

```php
Schema::create('blog_posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('excerpt')->nullable();
    $table->longText('content');
    $table->string('featured_image')->nullable();
    $table->foreignId('author_id')->nullable()->constrained('users');
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
});
```

#### 11. `product_attributes`

```php
Schema::create('product_attributes', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Color, Size, Material, etc.
    $table->string('slug');
    $table->enum('type', ['select', 'text', 'boolean', 'numeric']);
    $table->json('options')->nullable(); // For select type
    $table->boolean('is_required')->default(false);
    $table->timestamps();

    $table->unique('slug');
});
```

#### 12. `product_attribute_values`

```php
Schema::create('product_attribute_values', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
    $table->string('value');
    $table->decimal('price_adjustment', 10, 2)->default(0);
    $table->integer('stock_quantity')->default(0);
    $table->string('sku_suffix')->nullable();
    $table->primary(['product_id', 'attribute_id', 'value']);
});
```

#### 13. `reviews` (Enhance existing)

```php
Schema::table('reviews', function (Blueprint $table) {
    $table->text('admin_response')->nullable()->after('comment');
    $table->timestamp('responded_at')->nullable()->after('admin_response');
});
```

#### 14. `support_tickets`

```php
Schema::create('support_tickets', function (Blueprint $table) {
    $table->id();
    $table->string('ticket_number')->unique();
    $table->foreignId('user_id')->nullable()->constrained();
    $table->string('name')->nullable();
    $table->string('email');
    $table->string('subject');
    $table->enum('category', ['order', 'product', 'payment', 'shipping', 'return', 'other']);
    $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
    $table->enum('status', ['open', 'pending', 'resolved', 'closed'])->default('open');
    $table->timestamps();
});
```

#### 15. `support_messages`

```php
Schema::create('support_messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->nullable()->constrained();
    $table->boolean('is_admin')->default(false);
    $table->text('message');
    $table->json('attachments')->nullable();
    $table->timestamps();
});
```

#### 16. `vendors` (Multi-vendor)

```php
Schema::create('vendors', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->text('description')->nullable();
    $table->string('logo')->nullable();
    $table->string('banner')->nullable();
    $table->text('address')->nullable();
    $table->decimal('commission_rate', 5, 2)->default(10); // Percentage
    $table->decimal('balance', 10, 2)->default(0);
    $table->enum('status', ['pending', 'active', 'suspended', 'inactive'])->default('pending');
    $table->timestamps();
});
```

#### 17. `vendor_products` (Pivot with approval)

```php
Schema::create('vendor_products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->text('rejection_reason')->nullable();
    $table->timestamps();

    $table->unique(['vendor_id', 'product_id']);
});
```

### 3.2 Existing Table Updates

#### Users Table

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('admin_theme')->default('light')->after('is_active');
    $table->string('avatar')->nullable()->after('admin_theme');
});
```

#### Products Table

```php
Schema::table('products', function (Blueprint $table) {
    $table->string('video_url')->nullable()->after('meta_description');
    $table->integer('view_count')->default(0)->after('status');
    $table->integer('sold_count')->default(0)->after('view_count');
    $table->decimal('average_rating', 3, 2)->default(0)->after('sold_count');
});
```

#### Orders Table

```php
Schema::table('orders', function (Blueprint $table) {
    $table->string('invoice_number')->nullable()->unique()->after('user_id');
    $table->string('tracking_number')->nullable()->after('status');
    $table->string('shipping_carrier')->nullable()->after('tracking_number');
    $table->decimal('shipping_cost', 10, 2)->default(0)->after('total_amount');
    $table->decimal('tax_amount', 10, 2)->default(0)->after('shipping_cost');
    $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
    $table->string('coupon_code')->nullable()->after('discount_amount');
    $table->text('customer_notes')->nullable()->after('notes');
    $table->text('internal_notes')->nullable()->after('customer_notes');
    $table->timestamp('shipped_at')->nullable()->after('created_at');
    $table->timestamp('delivered_at')->nullable()->after('shipped_at');
});
```

#### Coupons Table

```php
Schema::table('coupons', function (Blueprint $table) {
    $table->enum('applies_to', ['all', 'specific_products', 'specific_categories', 'specific_customers'])->default('all');
    $table->json('applicable_ids')->nullable(); // Product/category IDs
    $table->integer('usage_limit_per_user')->nullable()->after('usage_limit');
    $table->boolean('is_first_order_only')->default(false)->after('usage_limit_per_user');
});
```

---

## 4. Controller Implementations

### 4.1 New Controllers

```
app/Http/Controllers/Admin/
├── AnalyticsController.php
│   └── index() - Dashboard analytics API
│       getSalesData() - Sales trends
│       getOrdersData() - Order analytics
│       getCustomerData() - Customer metrics
│       getProductPerformance() - Top products
│       exportReport() - PDF/Excel exports
│
├── FlashSaleController.php
│   └── index(), create(), store(), edit(), update(), destroy()
│   └── toggleStatus(), manageProducts()
│
├── LoyaltyController.php
│   └── index(), configure(), issueBonus()
│   └── viewTransactions(), settings()
│
├── ABCCController.php  // Abandoned Cart
│   └── index(), sendReminder(), recover()
│
├── RBACController.php  // Role-Based Access Control
│   └── roles(), permissions(), assignRole()
│   └── rolePermissions()
│
├── AuditLogController.php
│   └── index(), show(), export(), clear()
│
├── PageController.php  // CMS Pages
│   └── index(), create(), store(), edit(), update(), destroy()
│   └── toggleStatus()
│
├── BlogController.php
│   └── index(), create(), store(), edit(), update(), destroy()
│   └── categories(), comments()
│
├── SupportController.php  // Help Desk
│   └── tickets(), show(), reply(), updateStatus()
│
├── VendorController.php  // Multi-Vendor
│   └── index(), create(), store(), edit(), update(), destroy()
│   └── approve(), suspend(), products(), earnings()
│
├── InventoryAutomationController.php
│   └── rules(), create(), update(), toggle()
│   └── viewLogs(), executeNow()
│
└── SettingsController.php (Enhance)
    └── general(), payments(), shipping(), emails()
    └── seo(), system(), backup()
```

### 4.2 Existing Controller Enhancements

#### DashboardController

```php
public function index()
{
    // Enhanced with more stats and charts
    $stats = [
        'total_revenue' => Order::sum('total_amount') / 100,
        'revenue_today' => Order::whereDate('created_at', today())->sum('total_amount') / 100,
        'revenue_this_week' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount') / 100,
        'revenue_this_month' => Order::whereMonth('created_at', now()->month)->sum('total_amount') / 100,
        'orders_today' => Order::whereDate('created_at', today())->count(),
        'orders_this_week' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        'average_order_value' => Order::avg('total_amount') / 100,
        'customer_count' => User::where('is_admin', false)->count(),
        'new_customers_this_month' => User::whereMonth('created_at', now()->month)->count(),
        'low_stock_count' => Product::where('stock_quantity', '<=', 10)->count(),
        'pending_orders' => Order::where('status', 'pending')->count(),
        'conversion_rate' => $this->calculateConversionRate(),
    ];

    $charts = [
        'sales_trend' => $this->getSalesTrendData(30),
        'orders_by_status' => $this->getOrdersByStatusData(),
        'top_categories' => $this->getTopCategoriesData(),
    ];

    $recentOrders = Order::with('items.product', 'user')->latest()->take(10)->get();
    $topProducts = Product::with('brand')->orderBy('sold_count', 'desc')->take(10)->get();
    $lowStockAlerts = Product::where('stock_quantity', '<=', 10)->take(5)->get();

    return view('admin.dashboard', compact('stats', 'charts', 'recentOrders', 'topProducts', 'lowStockAlerts'));
}
```

#### ProductController (Enhance)

```php
public function bulkAction(Request $request)
{
    // Handle bulk activate, deactivate, delete, update stock
}

public function duplicate(Product $product)
{
    // Duplicate product with all attributes
}

public function export()
{
    // Export products to CSV/Excel
}

public function import(Request $request)
{
    // Import products from CSV/Excel
}
```

#### OrderController (Enhance)

```php
public function create()
{
    // Admin manual order creation
}

public function updateStatus(Request $request, Order $order)
{
    // Enhanced with status history
}

public function addNote(Request $request, Order $order)
{
    // Add internal notes
}

public function generateInvoice(Order $order)
{
    // Generate PDF invoice
}

public function export(Request $request)
{
    // Export orders to CSV/Excel
}
```

#### CustomerController (Enhance)

```php
public function index(Request $request)
{
    // Enhanced with filters: by orders count, by total spent, by registration date
}

public function segmentation()
{
    // View customer segments
}

public function export(Request $request)
{
    // Export customers
}
```

---

## 5. Route Structure

### 5.1 Updated Admin Routes

```php
// routes/admin.php

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/analytics/data', [AnalyticsController::class, 'getData'])->name('analytics.data');
Route::post('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');

// Products (Enhanced)
Route::resource('products', ProductController::class);
Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');
Route::post('products/duplicate/{product}', [ProductController::class, 'duplicate'])->name('products.duplicate');
Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
Route::post('products/import', [ProductController::class, 'import'])->name('products.import');

// Products → Attributes
Route::prefix('products/{product}/attributes')->name('products.attributes.')->group(function () {
    Route::get('/', [ProductAttributeController::class, 'index'])->name('index');
    Route::post('/', [ProductAttributeController::class, 'store'])->name('store');
    Route::put('/{attribute}', [ProductAttributeController::class, 'update'])->name('update');
    Route::delete('/{attribute}', [ProductAttributeController::class, 'destroy'])->name('destroy');
});

// Orders (Enhanced)
Route::resource('orders', OrderController::class);
Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
Route::post('orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');
Route::post('orders/{order}/invoice', [OrderController::class, 'generateInvoice'])->name('orders.invoice');
Route::post('orders/{order}/note', [OrderController::class, 'addNote'])->name('orders.note');
Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');

// Customers (Enhanced)
Route::resource('customers', CustomerController::class);
Route::post('customers/{customer}/toggle', [CustomerController::class, 'toggleActive'])->name('customers.toggle');
Route::get('customers/segmentation', [CustomerController::class, 'segmentation'])->name('customers.segmentation');
Route::post('customers/export', [CustomerController::class, 'export'])->name('customers.export');

// Marketing
Route::prefix('marketing')->name('marketing.')->group(function () {
    // Coupons
    Route::resource('coupons', CouponController::class);
    Route::post('coupons/{coupon}/toggle', [CouponController::class, 'toggleStatus'])->name('coupons.toggle');

    // Flash Sales
    Route::resource('flash-sales', FlashSaleController::class);
    Route::post('flash-sales/{flashSale}/toggle', [FlashSaleController::class, 'toggleStatus'])->name('flash-sales.toggle');

    // Loyalty Program
    Route::get('loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::put('loyalty/settings', [LoyaltyController::class, 'updateSettings'])->name('loyalty.settings');
    Route::post('loyalty/bonus', [LoyaltyController::class, 'issueBonus'])->name('loyalty.bonus');

    // Abandoned Carts
    Route::get('abandoned-carts', [ABCCController::class, 'index'])->name('abandoned-carts.index');
    Route::post('abandoned-carts/{cart}/reminder', [ABCCController::class, 'sendReminder'])->name('abandoned-carts.reminder');
    Route::post('abandoned-carts/{cart}/recover', [ABCCController::class, 'recover'])->name('abandoned-carts.recover');

    // Reviews
    Route::resource('reviews', ReviewController::class);
    Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
});

// Analytics
Route::prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
    Route::get('/sales', [AnalyticsController::class, 'sales'])->name('sales');
    Route::get('/products', [AnalyticsController::class, 'products'])->name('products');
    Route::get('/customers', [AnalyticsController::class, 'customers'])->name('customers');
    Route::get('/inventory', [AnalyticsController::class, 'inventory'])->name('inventory');
});

// Content Management
Route::prefix('content')->name('content.')->group(function () {
    // Pages
    Route::resource('pages', PageController::class);
    Route::post('pages/{page}/toggle', [PageController::class, 'toggleStatus'])->name('pages.toggle');

    // Blog
    Route::resource('blog', BlogController::class);
    Route::post('blog/{post}/publish', [BlogController::class, 'publish'])->name('blog.publish');
});

// Support (Help Desk)
Route::prefix('support')->name('support.')->group(function () {
    Route::get('tickets', [SupportController::class, 'index'])->name('tickets.index');
    Route::get('tickets/{ticket}', [SupportController::class, 'show'])->name('tickets.show');
    Route::post('tickets/{ticket}/reply', [SupportController::class, 'reply'])->name('tickets.reply');
    Route::patch('tickets/{ticket}/status', [SupportController::class, 'updateStatus'])->name('tickets.updateStatus');
});

// System & Security
Route::prefix('system')->name('system.')->group(function () {
    // RBAC
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.permissions');

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::delete('audit-logs/clear', [AuditLogController::class, 'clear'])->name('audit-logs.clear');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
});

// Multi-Vendor (Enterprise)
Route::prefix('vendors')->name('vendors.')->group(function () {
    Route::get('/', [VendorController::class, 'index'])->name('index');
    Route::get('/create', [VendorController::class, 'create'])->name('create');
    Route::post('/', [VendorController::class, 'store'])->name('store');
    Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
    Route::get('/{vendor}/edit', [VendorController::class, 'edit'])->name('edit');
    Route::put('/{vendor}', [VendorController::class, 'update'])->name('update');
    Route::delete('/{vendor}', [VendorController::class, 'destroy'])->name('destroy');
    Route::post('/{vendor}/approve', [VendorController::class, 'approve'])->name('approve');
    Route::post('/{vendor}/suspend', [VendorController::class, 'suspend'])->name('suspend');
    Route::get('/{vendor}/products', [VendorController::class, 'products'])->name('products');
    Route::get('/{vendor}/earnings', [VendorController::class, 'earnings'])->name('earnings');
});

// Inventory Automation (Enterprise)
Route::prefix('inventory/automation')->name('inventory.automation.')->group(function () {
    Route::get('/rules', [InventoryAutomationController::class, 'index'])->name('rules.index');
    Route::post('/rules', [InventoryAutomationController::class, 'store'])->name('rules.store');
    Route::put('/rules/{rule}', [InventoryAutomationController::class, 'update'])->name('rules.update');
    Route::delete('/rules/{rule}', [InventoryAutomationController::class, 'destroy'])->name('rules.destroy');
    Route::post('/rules/{rule}/toggle', [InventoryAutomationController::class, 'toggle'])->name('rules.toggle');
    Route::get('/logs', [InventoryAutomationController::class, 'viewLogs'])->name('logs.index');
});

// Settings (Enhanced)
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingController::class, 'edit'])->name('edit');
    Route::put('/', [SettingController::class, 'update'])->name('update');
    Route::put('/general', [SettingController::class, 'updateGeneral'])->name('general');
    Route::put('/payments', [SettingController::class, 'updatePayments'])->name('payments');
    Route::put('/shipping', [SettingController::class, 'updateShipping'])->name('shipping');
    Route::put('/emails', [SettingController::class, 'updateEmails'])->name('emails');
    Route::put('/seo', [SettingController::class, 'updateSeo'])->name('seo');
    Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('clear-cache');
    Route::get('/backup', [SettingController::class, 'backup'])->name('backup');
});

// Theme Settings
Route::post('/theme/toggle', [SettingController::class, 'toggleTheme'])->name('theme.toggle');
```

---

## 6. View Files Structure

### 6.1 Layout Components

```
resources/views/admin/
├── layout.blade.php              # Light mode (enhanced)
├── layout-dark.blade.php         # Dark mode (new)
└── components/
    ├── sidebar.blade.php         # Unified sidebar with theme detection
    ├── sidebar-item.blade.php    # Sidebar item component
    ├── header.blade.php          # Unified header with theme toggle
    ├── theme-toggle.blade.php    # Theme switcher
    ├── user-menu.blade.php       # User dropdown menu
    ├── notifications.blade.php  # Notification dropdown
    ├── breadcrumb.blade.php      # Breadcrumb navigation
    └── toast.blade.php           # Toast notification
```

### 6.2 Dashboard Views

```
resources/views/admin/dashboard/
├── index.blade.php               # Light mode dashboard
├── index-dark.blade.php          # Dark mode dashboard
└── components/
    ├── stats-grid.blade.php      # Stats cards grid
    ├── stats-card.blade.php      # Individual stat card
    ├── charts-section.blade.php   # Charts container
    ├── sales-chart.blade.php      # Sales trend line chart
    ├── orders-chart.blade.php    # Orders status doughnut
    ├── top-categories.blade.php  # Categories bar chart
    ├── recent-orders.blade.php   # Recent orders table
    ├── top-products.blade.php     # Top selling products
    ├── low-stock-alerts.blade.php # Low stock warnings
    └── quick-actions.blade.php    # Quick action buttons
```

### 6.3 Product Management Views

```
resources/views/admin/products/
├── index.blade.php               # Products list (enhanced)
├── create.blade.php              # Create product
├── edit.blade.php                # Edit product
├── show.blade.php                # Product details
├── variants.blade.php            # Product variants
├── bulk-import.blade.php         # Bulk import page
└── components/
    ├── product-card.blade.php
    ├── product-table.blade.php
    ├── image-manager.blade.php
    └── attribute-row.blade.php
```

### 6.4 Order Management Views

```
resources/views/admin/orders/
├── index.blade.php               # Orders list (enhanced)
├── show.blade.php                # Order details (enhanced)
├── create.blade.php              # Manual order creation
├── invoice.blade.php             # Invoice template
└── components/
    ├── order-card.blade.php
    ├── order-table.blade.php
    ├── order-items.blade.php
    ├── order-timeline.blade.php  # Status history
    └── status-badge.blade.php
```

### 6.5 Marketing Views

```
resources/views/admin/marketing/
├── coupons/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── flash-sales/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── loyalty/
│   ├── index.blade.php
│   └── settings.blade.php
├── abandoned-carts/
│   ├── index.blade.php
│   └── cart-detail.blade.php
└── reviews/
    ├── index.blade.php
    └── show.blade.php
```

### 6.6 Analytics Views

```
resources/views/admin/analytics/
├── index.blade.php               # Overview dashboard
├── sales.blade.php               # Sales analytics
├── products.blade.php            # Product performance
├── customers.blade.php           # Customer analytics
├── inventory.blade.php           # Inventory reports
└── components/
    ├── date-range-picker.blade.php
    ├── export-button.blade.php
    └── report-table.blade.php
```

### 6.7 Content Management Views

```
resources/views/admin/content/
├── pages/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── blog/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

### 6.8 Support Views

```
resources/views/admin/support/
├── tickets/
│   ├── index.blade.php
│   ├── show.blade.php
│   └── components/
│       ├── ticket-list.blade.php
│       ├── message.blade.php
│       └── reply-form.blade.php
└── settings.blade.php
```

### 6.9 System Views

```
resources/views/admin/system/
├── roles/
│   ├── index.blade.php
│   └── edit.blade.php
├── permissions/
│   └── index.blade.php
└── audit-logs/
    ├── index.blade.php
    └── detail.blade.php
```

### 6.10 Vendor Management Views

```
resources/views/admin/vendors/
├── index.blade.php
├── create.blade.php
├── show.blade.php
├── edit.blade.php
├── products.blade.php
└── earnings.blade.php
```

---

## 7. Implementation Phases

### Phase 1: Foundation (Week 1-2)

1. **Theme System Setup**
    - Create dark mode CSS variables
    - Build layout-dark.blade.php
    - Implement theme toggle component
    - Update all existing views for dark mode compatibility

2. **Enhanced Dashboard**
    - Install Chart.js
    - Create stats-card component
    - Build sales trend chart
    - Create orders by status chart
    - Add top products section
    - Implement quick actions bar

3. **Database Updates**
    - Add theme column to users
    - Add activity_logs indexes
    - Create base tables for new features

### Phase 2: Core Features (Week 3-4)

1. **Product Management**
    - Bulk actions
    - Duplicate product
    - Export/Import
    - Product attributes/variants

2. **Order Management**
    - Manual order creation
    - Invoice generation
    - Enhanced order tracking
    - Order notes system

3. **Customer CRM**
    - Customer segmentation
    - Export functionality
    - Customer history timeline

### Phase 3: Marketing (Week 5-6)

1. **Coupon System**
    - Advanced coupon rules
    - Usage limits per user
    - Product/category specific coupons

2. **Flash Sales**
    - Flash sale management
    - Countdown timers
    - Stock allocation

3. **Loyalty Program**
    - Points configuration
    - Transaction history
    - Bonus points system

4. **Abandoned Carts**
    - Cart recovery
    - Email reminders
    - Recovery tracking

### Phase 4: Analytics & Reports (Week 7-8)

1. **Advanced Analytics**
    - Sales trends
    - Customer behavior
    - Product performance
    - Inventory insights

2. **Export System**
    - PDF reports
    - Excel exports
    - Scheduled reports

### Phase 5: Content & Support (Week 9-10)

1. **CMS**
    - Page builder
    - Blog management
    - SEO settings

2. **Help Desk**
    - Ticket system
    - Customer communication
    - Knowledge base

### Phase 6: Security & Enterprise (Week 11-12)

1. **RBAC System**
    - Roles management
    - Permissions system
    - Access control middleware

2. **Audit Logging**
    - Activity tracking
    - Log viewer
    - Export logs

3. **Multi-Vendor (Optional)**
    - Vendor registration
    - Commission management
    - Vendor products

4. **Inventory Automation**
    - Low stock rules
    - Auto reorder
    - Alert system

---

## 8. Component Library

Create reusable Blade components for consistent UI:

```
resources/views/components/admin/
├── ui/
│   ├── button.blade.php
│   ├── input.blade.php
│   ├── select.blade.php
│   ├── textarea.blade.php
│   ├── checkbox.blade.php
│   ├── radio.blade.php
│   ├── toggle.blade.php
│   ├── modal.blade.php
│   ├── alert.blade.php
│   ├── badge.blade.php
│   ├── card.blade.php
│   ├── table.blade.php
│   ├── pagination.blade.php
│   ├── dropdown.blade.php
│   ├── tab.blade.php
│   ├── accordion.blade.php
│   └── tooltip.blade.php
│
├── forms/
│   ├── field.blade.php
│   ├── field-error.blade.php
│   ├── label.blade.php
│   ├── help-text.blade.php
│   └── form-group.blade.php
│
├── charts/
│   ├── line-chart.blade.php
│   ├── bar-chart.blade.php
│   ├── doughnut-chart.blade.php
│   └── chart-container.blade.php
│
├── layout/
│   ├── page-header.blade.php
│   ├── section.blade.php
│   ├── sidebar-nav.blade.php
│   └── breadcrumbs.blade.php
│
└── data/
    ├── data-table.blade.php
    ├── stat-card.blade.php
    ├── progress-bar.blade.php
    └── empty-state.blade.php
```

---

## 9. CSS/Tailwind Architecture

### 9.1 CSS Variables for Theming

```css
/* Light Mode (default) */
:root {
    --background: 0 0% 100%;
    --foreground: 222.2 84% 4.9%;
    --card: 0 0% 100%;
    --card-foreground: 222.2 84% 4.9%;
    --popover: 0 0% 100%;
    --popover-foreground: 222.2 84% 4.9%;
    --primary: 178 99% 28%;
    --primary-foreground: 0 0% 100%;
    --secondary: 210 40% 96%;
    --secondary-foreground: 222.2 47% 11.2%;
    --muted: 210 40% 96%;
    --muted-foreground: 215.4 16.3% 46.9%;
    --accent: 210 40% 96%;
    --accent-foreground: 222.2 47% 11.2%;
    --destructive: 0 84.2% 60.2%;
    --destructive-foreground: 210 40% 98%;
    --border: 214.3 31.8% 91.4%;
    --input: 214.3 31.8% 91.4%;
    --ring: 178 99% 28%;
    --radius: 0.5rem;
}

/* Dark Mode */
.dark {
    --background: 222.2 84% 4.9%;
    --foreground: 210 40% 98%;
    --card: 222.2 84% 4.9%;
    --card-foreground: 210 40% 98%;
    --popover: 222.2 84% 4.9%;
    --popover-foreground: 210 40% 98%;
    --primary: 178 99% 32%;
    --primary-foreground: 210 40% 98%;
    --secondary: 217.2 32.6% 17.5%;
    --secondary-foreground: 210 40% 98%;
    --muted: 217.2 32.6% 17.5%;
    --muted-foreground: 215 20.2% 65.1%;
    --accent: 217.2 32.6% 17.5%;
    --accent-foreground: 210 40% 98%;
    --destructive: 0 62.8% 30.6%;
    --destructive-foreground: 210 40% 98%;
    --border: 217.2 32.6% 17.5%;
    --input: 217.2 32.6% 17.5%;
    --ring: 178 99% 32%;
}
```

### 9.2 Tailwind Configuration

```javascript
// tailwind.config.js
module.exports = {
    darkMode: "class",
    content: ["./resources/views/**/*.blade.php"],
    theme: {
        extend: {
            colors: {
                border: "hsl(var(--border))",
                input: "hsl(var(--input))",
                ring: "hsl(var(--ring))",
                background: "hsl(var(--background))",
                foreground: "hsl(var(--foreground))",
                primary: {
                    DEFAULT: "hsl(var(--primary))",
                    foreground: "hsl(var(--primary-foreground))",
                },
                secondary: {
                    DEFAULT: "hsl(var(--secondary))",
                    foreground: "hsl(var(--secondary-foreground))",
                },
                destructive: {
                    DEFAULT: "hsl(var(--destructive))",
                    foreground: "hsl(var(--destructive-foreground))",
                },
                muted: {
                    DEFAULT: "hsl(var(--muted))",
                    foreground: "hsl(var(--muted-foreground))",
                },
                accent: {
                    DEFAULT: "hsl(var(--accent))",
                    foreground: "hsl(var(--accent-foreground))",
                },
                card: {
                    DEFAULT: "hsl(var(--card))",
                    foreground: "hsl(var(--card-foreground))",
                },
            },
            borderRadius: {
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },
        },
    },
    plugins: [],
};
```

---

## 10. Middleware & Security

### 10.1 New Middleware

```php
app/Http/Middleware/AdminTheme.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminTheme
{
    public function handle(Request $request, Closure $next): Response
    {
        // Apply theme class to response
        $theme = auth()->user()?->admin_theme ?? 'light';

        if ($theme === 'dark') {
            $request->session()->put('admin_theme', 'dark');
        }

        return $next($request);
    }
}
```

### 10.2 Permission Middleware

```php
app/Http/Middleware/CheckPermission.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->user()?->hasPermission($permission)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
```

### 10.3 Audit Logging Middleware

```php
app/Http/Middleware/AuditLog.php
<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLog
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log only specific methods
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $request->method(),
                'model_type' => $this->getModelType($request),
                'model_id' => $request->route('id'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    private function getModelType(Request $request): string
    {
        // Extract model type from route or request
        return match (true) {
            $request->routeIs('admin.products.*') => 'App\Models\Product',
            $request->routeIs('admin.orders.*') => 'App\Models\Order',
            $request->routeIs('admin.customers.*') => 'App\Models\User',
            default => 'Unknown',
        };
    }
}
```

---

## 11. API Endpoints (Optional)

Create API routes for potential mobile app or SPA integration:

```php
// routes/api.php

Route::prefix('api/admin')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard Stats
    Route::get('/stats', [DashboardController::class, 'apiStats']);

    // Products
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::get('/products/{product}', [ProductController::class, 'apiShow']);

    // Orders
    Route::get('/orders', [OrderController::class, 'apiIndex']);
    Route::get('/orders/{order}', [OrderController::class, 'apiShow']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'apiUpdateStatus']);

    // Customers
    Route::get('/customers', [CustomerController::class, 'apiIndex']);
    Route::get('/customers/{customer}', [CustomerController::class, 'apiShow']);

    // Analytics
    Route::get('/analytics/sales', [AnalyticsController::class, 'apiSales']);
    Route::get('/analytics/products', [AnalyticsController::class, 'apiProducts']);

    // Reports
    Route::get('/reports/export/{type}', [ReportController::class, 'apiExport']);
});
```

---

## 12. Testing Strategy

### 12.1 Feature Tests

```php
tests/Feature/Admin/
├── DashboardTest.php
├── ProductManagementTest.php
├── OrderManagementTest.php
├── CustomerManagementTest.php
├── MarketingTest.php
└── AnalyticsTest.php
```

### 12.2 Unit Tests

```php
tests/Unit/
├── Services/
│   ├── OrderServiceTest.php
│   ├── ProductServiceTest.php
│   └── LoyaltyServiceTest.php
├── Models/
│   ├── OrderTest.php
│   ├── ProductTest.php
│   └── CouponTest.php
└── Helpers/
    └── CurrencyHelperTest.php
```

---

## 13. Documentation

### 13.1 Admin Documentation Structure

```
docs/admin/
├── getting-started.md
├── dashboard.md
├── catalog-management.md
├── order-fulfillment.md
├── customer-management.md
├── marketing-tools.md
├── analytics.md
├── content-management.md
├── system-settings.md
└── faq.md
```

---

## 14. Estimated Timeline

| Phase                        | Duration | Key Deliverables                      |
| ---------------------------- | -------- | ------------------------------------- |
| Phase 1: Foundation          | 2 weeks  | Dual theme system, enhanced dashboard |
| Phase 2: Core Features       | 2 weeks  | Product/Order/Customer enhancements   |
| Phase 3: Marketing           | 2 weeks  | Coupons, Flash Sales, Loyalty, ABC    |
| Phase 4: Analytics           | 2 weeks  | Advanced reports, exports             |
| Phase 5: Content/Support     | 2 weeks  | CMS, Help Desk                        |
| Phase 6: Security/Enterprise | 2 weeks  | RBAC, Audit Logs, Multi-Vendor        |

**Total Estimated Time: 12-14 weeks**

---

## 15. Priority Matrix

### Must Have (Phase 1-2)

- [x] Dark/Light theme toggle
- [x] Enhanced dashboard with charts
- [x] Stats cards (8 metrics)
- [x] Product bulk actions
- [x] Order invoice generation
- [x] Customer segmentation

### Should Have (Phase 3-4)

- [ ] Flash sales
- [ ] Loyalty program
- [ ] Abandoned cart recovery
- [ ] Advanced analytics
- [ ] PDF reports

### Could Have (Phase 5-6)

- [ ] Help desk
- [ ] Blog management
- [ ] Multi-vendor
- [ ] Inventory automation

---

## 16. Next Steps

1. **Review and approve** this implementation plan
2. **Prioritize features** based on business needs
3. **Begin Phase 1**: Start with theme system and enhanced dashboard
4. **Iterate** based on feedback and testing

---

_Plan Version: 1.0_
_Last Updated: 2026-02-05_
_Project: Kiddo's Heaven E-Commerce Admin Panel_
