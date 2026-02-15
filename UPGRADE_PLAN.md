# 🚀 **COMPLETE UPGRADE & IMPLEMENTATION PLAN**
## Kiddo's Heaven → Enterprise SaaS E-commerce Platform (Shopify Competitor)

**Target:** Multi-tenant SaaS platform with Livewire 4, industry-standard architecture, Shopify-level features

**Timeline:** 12-16 weeks (3-4 months)

**Team:** Claude (AI Developer)

**Status:** Planning Phase

---

## 📋 **TABLE OF CONTENTS**

1. [Project Goals & Vision](#1-project-goals--vision)
2. [Current State Analysis](#2-current-state-analysis)
3. [Target Architecture](#3-target-architecture)
4. [Technology Stack](#4-technology-stack)
5. [Implementation Phases](#5-implementation-phases)
6. [Detailed Task Breakdown](#6-detailed-task-breakdown)
7. [Database Schema Changes](#7-database-schema-changes)
8. [Code Structure & Organization](#8-code-structure--organization)
9. [Testing Strategy](#9-testing-strategy)
10. [Deployment Plan](#10-deployment-plan)
11. [Risk Mitigation](#11-risk-mitigation)
12. [Success Metrics](#12-success-metrics)

---

## 1️⃣ **PROJECT GOALS & VISION**

### **Primary Goal**
Transform Kiddo's Heaven from a single-tenant e-commerce application into a **multi-tenant SaaS platform** that competes with Shopify.

### **Key Objectives**

| Objective | Description | Success Criteria |
|-----------|-------------|------------------|
| **Multi-Tenancy** | Support unlimited tenants with subdomain/custom domain routing | Tenant isolation, separate databases per tenant |
| **Livewire 4 Migration** | Migrate entire frontend from Blade to Livewire 4 | 100% reactive components, no full page reloads |
| **Shopify-Level Features** | Match Shopify's core feature set | Feature parity checklist 90%+ complete |
| **Industry Standards** | Follow Laravel best practices, SOLID principles | PSR-12, 90%+ test coverage |
| **Performance** | Sub-2s page loads, support 100k+ products per tenant | Load testing benchmarks met |
| **Scalability** | Handle 1000+ tenants, 10k concurrent users | Infrastructure tested |

### **Shopify Feature Parity Checklist**

#### **Core Features (Must Have)**
- ✅ Multi-tenant architecture (subdomain + custom domains)
- ✅ **Catalogs (B2B/B2C/Regional product sets)** - NEW REQUIREMENT
- ✅ Product management (unlimited products, variants, collections)
- ✅ Inventory tracking (multi-location, FIFO, batch tracking)
- ✅ Order management (statuses, fulfillment, tracking, notes)
- ✅ Customer management (accounts, segments, lifetime value)
- ✅ Payment processing (Stripe, PayPal, COD, custom gateways)
- ✅ Shipping (rates, zones, carriers, tracking)
- ✅ Discounts & promotions (coupons, flash sales, bulk pricing)
- ✅ Themes (customizable storefront templates)
- ✅ Apps/Extensions (plugin architecture)
- ✅ Analytics & reports (sales, inventory, customers, 20+ reports)
- ✅ SEO tools (meta tags, sitemaps, structured data)
- ✅ Multi-currency support
- ✅ Multi-language support (i18n)
- ✅ API (REST + GraphQL for integrations)
- ✅ Webhooks (event-driven integrations)
- ✅ Subscription billing (for SaaS plans: Starter, Professional, Enterprise)

#### **Advanced Features (Nice to Have)**
- ⚠️ Point of Sale (POS) integration
- ⚠️ Abandoned cart recovery
- ⚠️ Email marketing automation
- ⚠️ Social media integration
- ⚠️ Product recommendations (AI/ML)
- ⚠️ Advanced analytics (cohort, funnel, attribution)

---

## 2️⃣ **CURRENT STATE ANALYSIS**

### **Current Architecture**
```
Single Tenant Application
├── Laravel 11
├── Blade Templates (traditional server-rendered)
├── Alpine.js (basic interactivity)
├── Tailwind CSS v4
├── Session-based cart
├── Repository + Service pattern
└── MySQL database (single)
```

### **Current Issues**

| Issue | Impact | Priority |
|-------|--------|----------|
| No multi-tenancy | Can't support multiple businesses | CRITICAL |
| Blade-only views | Full page reloads, poor UX | HIGH |
| No caching layer | Slow performance | HIGH |
| N+1 queries | Database bottleneck | HIGH |
| Session cart | Can't sync across devices | MEDIUM |
| Duplicate code | Hard to maintain | MEDIUM |
| No API | Can't integrate with external tools | MEDIUM |
| No webhooks | Can't trigger external events | LOW |

### **Current Strengths to Keep**

✅ **Excellent mobile-first design** (bottom nav, drawer filters)
✅ **Comprehensive admin panel** (54+ components)
✅ **Advanced inventory** (FIFO, batch tracking, COGS)
✅ **Financial tracking** (partners, investments, profit analysis)
✅ **Dark mode support**
✅ **Responsive Tailwind CSS**

---

## 3️⃣ **TARGET ARCHITECTURE**

### **Multi-Tenant SaaS Architecture**

```
┌─────────────────────────────────────────────────────────────┐
│                    TENANT ROUTING LAYER                     │
│  (Subdomain/Domain Detection → Tenant Context)              │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                   APPLICATION LAYER                         │
│  Laravel 11 + Livewire 4 + Filament Admin                  │
│  - Livewire Components (100% reactive)                      │
│  - Filament Admin Panel (modern admin UI)                   │
│  - API Layer (REST + GraphQL)                               │
│  - Webhook System (event broadcasting)                      │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    SERVICE LAYER                            │
│  - Multi-tenant Services (scoped to tenant context)         │
│  - Payment Gateways (Stripe, PayPal, etc.)                  │
│  - Email Service (transactional + marketing)                │
│  - Storage Service (S3/CDN for tenant assets)               │
│  - Queue Workers (background jobs)                          │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                    DATA LAYER                               │
│  Multi-Tenant Database Strategy:                            │
│  - Central DB (tenants, subscriptions, billing)             │
│  - Tenant DBs (isolated data per tenant)                    │
│  - Redis (cache + sessions + queues)                        │
│  - Elasticsearch (search)                                   │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                 INFRASTRUCTURE LAYER                        │
│  - CDN (CloudFlare/AWS CloudFront)                          │
│  - S3 (tenant assets, media files)                          │
│  - Load Balancer (horizontal scaling)                       │
│  - Queue Workers (Laravel Horizon)                          │
│  - Monitoring (Laravel Telescope + Sentry)                  │
└─────────────────────────────────────────────────────────────┘
```

### **Tenant Isolation Strategy**

**Option 1: Database Per Tenant (Recommended for SaaS)**
```
central_database
├── tenants (tenant metadata, domains, status)
├── subscriptions (billing, plans)
└── admins (super admin users)

tenant_1_database (shop1.example.com)
├── catalogs (B2B, B2C, Regional)
├── products (linked to catalogs)
├── categories (within catalogs)
├── orders
├── customers
└── [all tenant-specific data]

tenant_2_database (shop2.example.com)
├── catalogs (B2B, B2C, Regional)
├── products (linked to catalogs)
├── categories (within catalogs)
├── orders
├── customers
└── [all tenant-specific data]
```

**Why Database Per Tenant?**
- ✅ **Complete isolation** (tenant data never mixed)
- ✅ **Easy backups** (restore single tenant without affecting others)
- ✅ **Scalability** (distribute tenants across database servers)
- ✅ **Security** (data breach affects only one tenant)
- ✅ **Performance** (queries don't scan all tenants' data)

**Trade-off:** More complex migrations (must run on all tenant DBs)

---

## 4️⃣ **TECHNOLOGY STACK**

### **Backend**

| Technology | Version | Purpose |
|------------|---------|---------|
| **Laravel** | 11.x | Framework |
| **Livewire** | 4.x | Reactive components |
| **Filament** | 3.x | Admin panel framework |
| **Tenancy for Laravel** | 3.x | Multi-tenancy (stancl/tenancy) |
| **Spatie Permission** | 6.x | Role-based access control |
| **Spatie Media Library** | 11.x | File uploads & management |
| **Laravel Cashier** | 15.x | Subscription billing (Stripe) |
| **Laravel Sanctum** | 4.x | API authentication |
| **Laravel Horizon** | 5.x | Queue monitoring |
| **Laravel Telescope** | 5.x | Debugging (dev only) |

### **Frontend**

| Technology | Version | Purpose |
|------------|---------|---------|
| **Livewire** | 4.x | Reactive UI components |
| **Alpine.js** | 3.x | Client-side interactivity |
| **Tailwind CSS** | 4.x | Styling |
| **ApexCharts** | 3.x | Charts & graphs |
| **TomSelect** | 2.x | Select dropdowns |
| **Flatpickr** | 4.x | Date pickers |

### **Database & Cache**

| Technology | Version | Purpose |
|------------|---------|---------|
| **MySQL** | 8.0+ | Primary database |
| **Redis** | 7.x | Cache + sessions + queues |
| **Elasticsearch** | 8.x | Search (optional, Phase 5) |

### **Infrastructure**

| Service | Purpose |
|---------|---------|
| **AWS S3** | Tenant media storage |
| **CloudFlare** | CDN + DDoS protection |
| **Laravel Forge** | Server management |
| **AWS SES** | Transactional emails |
| **Stripe** | Payment processing + subscriptions |
| **Sentry** | Error tracking |

---

## 5️⃣ **IMPLEMENTATION PHASES**

### **Phase Overview**

| Phase | Duration | Focus | Deliverables |
|-------|----------|-------|--------------|
| **Phase 0** | 1 week | Setup & Planning | Dev environment, project structure |
| **Phase 1** | 2 weeks | Backend Foundation | Migrations, seeders, multi-tenancy |
| **Phase 2** | 2 weeks | Livewire Migration (Storefront) | All shop views → Livewire |
| **Phase 3** | 3 weeks | Filament Admin | Replace custom admin with Filament |
| **Phase 4** | 2 weeks | API & Integrations | REST API, webhooks, payment gateways |
| **Phase 5** | 2 weeks | SaaS Features | Tenant onboarding, billing, plans |
| **Phase 6** | 2 weeks | Performance & Scale | Caching, CDN, queues, optimization |
| **Phase 7** | 2 weeks | Testing & Deployment | End-to-end tests, staging, production |

**Total Duration:** 16 weeks (4 months)

---

## 6️⃣ **DETAILED TASK BREAKDOWN**

### **PHASE 0: Setup & Planning (Week 1)**

#### **Environment Setup**
```bash
# Local Development
- Install Redis
- Install MySQL 8.0
- Install Node.js 20+
- Configure Laravel Valet/Herd (Mac) or Laragon (Windows)
- Set up `.env` with proper database credentials

# Tools
- Install Laravel Telescope (dev)
- Install Laravel Debugbar (dev)
- Install PHP CodeSniffer (PSR-12)
- Install PHPStan (level 8)
```

#### **Project Structure Refactor**

**Current Structure:**
```
app/
├── Http/Controllers/
├── Models/
├── Services/
└── Repositories/
```

**Target Structure (Domain-Driven Design):**
```
app/
├── Domain/                    # Business logic (domain-driven)
│   ├── Catalog/               # NEW: Catalog domain
│   │   ├── Models/
│   │   │   └── Catalog.php
│   │   ├── Actions/
│   │   │   ├── CreateCatalog.php
│   │   │   ├── UpdateCatalog.php
│   │   │   ├── SwitchCatalog.php
│   │   │   └── SyncCatalogProducts.php
│   │   ├── Services/
│   │   │   └── CatalogService.php
│   │   └── Events/
│   │       ├── CatalogCreated.php
│   │       └── CatalogSwitched.php
│   ├── Product/
│   │   ├── Models/
│   │   │   ├── Product.php
│   │   │   ├── ProductVariant.php
│   │   │   └── Category.php
│   │   ├── Actions/           # Single-purpose actions
│   │   │   ├── CreateProduct.php
│   │   │   ├── UpdateProduct.php
│   │   │   └── DeleteProduct.php
│   │   ├── QueryBuilders/     # Eloquent query builders
│   │   │   └── ProductQueryBuilder.php
│   │   ├── Collections/       # Custom collections
│   │   │   └── ProductCollection.php
│   │   └── Events/            # Domain events
│   │       ├── ProductCreated.php
│   │       └── ProductUpdated.php
│   ├── Order/
│   │   ├── Models/
│   │   ├── Actions/
│   │   ├── Services/
│   │   └── Events/
│   ├── Customer/
│   ├── Inventory/
│   └── Tenant/
├── Livewire/                  # Livewire components
│   ├── Storefront/
│   │   ├── ProductCatalog.php
│   │   ├── ProductDetails.php
│   │   ├── ShoppingCart.php
│   │   └── Checkout.php
│   ├── Admin/                 # Fallback admin components
│   └── Shared/                # Shared components (search, notifications)
├── Filament/                  # Filament admin customizations
│   ├── Resources/
│   ├── Pages/
│   └── Widgets/
├── Http/
│   ├── Controllers/
│   │   └── Api/               # API controllers
│   ├── Middleware/
│   │   ├── IdentifyTenant.php
│   │   └── InitializeTenancyByDomain.php
│   └── Requests/              # Form requests
├── Jobs/                      # Queue jobs
├── Listeners/                 # Event listeners
├── Providers/
└── Support/                   # Helper classes
    ├── Enums/
    ├── Traits/
    └── Helpers/
```

#### **Composer Packages Installation**

```bash
# Multi-tenancy
composer require stancl/tenancy

# Livewire & UI
composer require livewire/livewire:^3.0
composer require filament/filament:^3.0

# Additional packages
composer require spatie/laravel-permission
composer require spatie/laravel-medialibrary
composer require spatie/laravel-backup
composer require laravel/cashier
composer require laravel/sanctum
composer require laravel/horizon

# Development
composer require --dev laravel/telescope
composer require --dev barryvdh/laravel-debugbar
composer require --dev nunomaduro/larastan
```

#### **Tasks**

| Task | Estimated Time | Status |
|------|----------------|--------|
| Set up local development environment | 4h | Pending |
| Install all composer packages | 2h | Pending |
| Refactor project structure (move files to Domain/) | 8h | Pending |
| Set up code quality tools (PHPStan, Pint) | 2h | Pending |
| Create development database | 1h | Pending |
| Configure Redis | 1h | Pending |
| Set up Git workflow (main, develop, feature branches) | 2h | Pending |

**Total Phase 0:** 20 hours (~1 week)

---

### **PHASE 1: Backend Foundation (Weeks 2-3)**

#### **1.1 Multi-Tenancy Setup**

**Install Tenancy Package**
```bash
composer require stancl/tenancy
php artisan tenancy:install
```

**Configure Tenancy** (`config/tenancy.php`)
```php
return [
    'tenant_model' => \App\Domain\Tenant\Models\Tenant::class,
    'id_generator' => \Stancl\Tenancy\TenantDatabaseManagers\UUIDGenerator::class,

    'database' => [
        'central_domains' => [
            'central' => env('DB_DATABASE', 'central'),
        ],
        'template_tenant_connection' => null,
        'managers' => [
            'database' => \Stancl\Tenancy\TenantDatabaseManagers\DatabaseManager::class,
        ],
    ],

    'middleware' => [
        'main' => 'tenancy',
        'universal' => 'universal',
    ],

    'features' => [
        Stancl\Tenancy\Features\TenantConfig::class,
        Stancl\Tenancy\Features\TenantDomains::class,
    ],
];
```

**Central Database Migration** (tenants table)
```php
// database/migrations/central/2024_01_01_create_tenants_table.php
Schema::create('tenants', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->string('subdomain')->unique();
    $table->string('database')->unique();
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->string('status')->default('active'); // active, suspended, cancelled
    $table->string('plan')->default('starter'); // starter, professional, enterprise
    $table->timestamp('trial_ends_at')->nullable();
    $table->timestamp('subscribed_at')->nullable();
    $table->json('settings')->nullable();
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('domains', function (Blueprint $table) {
    $table->id();
    $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
    $table->string('domain')->unique();
    $table->boolean('is_primary')->default(false);
    $table->timestamps();
});
```

**Tenant Database Migrations** (all existing tables + new fields)
```php
// database/migrations/tenant/2024_01_01_create_catalogs_table.php
Schema::create('catalogs', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->enum('type', ['b2b', 'b2c', 'regional', 'wholesale', 'retail'])->default('b2c');
    $table->string('target_audience')->nullable(); // 'businesses', 'consumers', 'both'
    $table->json('region_codes')->nullable(); // ['US', 'CA', 'UK'] for regional catalogs
    $table->string('currency')->default('BDT');
    $table->string('language')->default('en');
    $table->boolean('is_active')->default(true)->index();
    $table->boolean('is_default')->default(false); // One default catalog per tenant
    $table->integer('sort_order')->default(0);
    $table->json('settings')->nullable(); // Custom settings per catalog
    $table->json('pricing_rules')->nullable(); // Catalog-specific pricing
    $table->timestamps();
    $table->softDeletes();

    $table->index('type');
    $table->index(['is_active', 'is_default']);
});

// database/migrations/tenant/2024_01_01_create_products_table.php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('catalog_id')->nullable()->constrained()->nullOnDelete(); // NEW: Catalog relationship
    $table->string('name');
    $table->string('slug'); // NOT unique anymore (unique per catalog)
    $table->text('short_description')->nullable();
    $table->longText('description')->nullable();
    $table->string('sku')->nullable(); // NOT unique anymore (unique per catalog)
    $table->decimal('price', 10, 2);
    $table->decimal('cost_price', 10, 2)->nullable();
    $table->decimal('compare_at_price', 10, 2)->nullable();
    $table->integer('stock_quantity')->default(0);
    $table->enum('product_type', ['simple', 'variable', 'digital'])->default('simple');
    $table->enum('delivery_type', ['standard', 'express', 'digital'])->default('standard');
    $table->boolean('is_active')->default(true)->index();
    $table->boolean('is_featured')->default(false)->index();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
    $table->string('primary_image')->nullable();
    $table->json('images')->nullable();
    $table->json('tags')->nullable();
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->string('meta_keywords')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // Indexes for performance
    $table->index('created_at');
    $table->index(['catalog_id', 'is_active', 'is_featured']);
    $table->unique(['catalog_id', 'slug']); // Slug unique per catalog
    $table->unique(['catalog_id', 'sku']); // SKU unique per catalog
});

// database/migrations/tenant/2024_01_01_create_categories_table.php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('catalog_id')->nullable()->constrained()->cascadeOnDelete(); // NEW: Catalog relationship
    $table->string('name');
    $table->string('slug');
    $table->text('description')->nullable();
    $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
    $table->string('icon')->nullable();
    $table->string('image')->nullable();
    $table->boolean('is_active')->default(true);
    $table->boolean('show_on_home')->default(false);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    $table->softDeletes();

    $table->index(['catalog_id', 'is_active']);
    $table->unique(['catalog_id', 'slug']); // Slug unique per catalog
});

// ... (all other tables: orders, customers, etc.)
```

**Tenant Model**
```php
// app/Domain/Tenant/Models/Tenant.php
namespace App\Domain\Tenant\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'name', 'subdomain', 'email', 'phone',
        'status', 'plan', 'trial_ends_at', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
        'subscribed_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id', 'name', 'subdomain', 'email',
            'phone', 'status', 'plan', 'trial_ends_at'
        ];
    }

    // Helper methods
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function isSubscribed(): bool
    {
        return $this->subscribed_at !== null;
    }

    public function canAccessFeature(string $feature): bool
    {
        $planFeatures = [
            'starter' => ['products' => 100, 'storage_gb' => 5, 'catalogs' => 1],
            'professional' => ['products' => 1000, 'storage_gb' => 50, 'catalogs' => 5],
            'enterprise' => ['products' => -1, 'storage_gb' => -1, 'catalogs' => -1], // unlimited
        ];

        return isset($planFeatures[$this->plan][$feature]);
    }
}

// app/Domain/Catalog/Models/Catalog.php
namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Catalog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'type', 'target_audience',
        'region_codes', 'currency', 'language', 'is_active', 'is_default',
        'sort_order', 'settings', 'pricing_rules'
    ];

    protected $casts = [
        'region_codes' => 'array',
        'settings' => 'array',
        'pricing_rules' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeB2B($query)
    {
        return $query->where('type', 'b2b');
    }

    public function scopeB2C($query)
    {
        return $query->where('type', 'b2c');
    }

    public function scopeRegional($query, ?string $regionCode = null)
    {
        $query = $query->where('type', 'regional');

        if ($regionCode) {
            $query->whereJsonContains('region_codes', $regionCode);
        }

        return $query;
    }

    // Helper methods
    public function getActiveProductsCount(): int
    {
        return $this->products()->where('is_active', true)->count();
    }

    public function isRegional(): bool
    {
        return $this->type === 'regional';
    }

    public function isB2B(): bool
    {
        return $this->type === 'b2b';
    }

    public function isB2C(): bool
    {
        return $this->type === 'b2c';
    }

    public function applyPricingRules(float $basePrice): float
    {
        if (!$this->pricing_rules) {
            return $basePrice;
        }

        $discount = $this->pricing_rules['discount_percentage'] ?? 0;
        return $basePrice * (1 - $discount / 100);
    }
}
```

#### **1.2 Database Schema Refactor**

**Add Missing Industry-Standard Fields**

```php
// Enhance products table
Schema::table('products', function (Blueprint $table) {
    // SEO
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->string('meta_keywords')->nullable();

    // Additional fields
    $table->integer('sold_count')->default(0)->index();
    $table->decimal('average_rating', 3, 2)->default(0);
    $table->integer('review_count')->default(0);
    $table->timestamp('published_at')->nullable();

    // Inventory
    $table->integer('low_stock_threshold')->default(10);
    $table->boolean('track_inventory')->default(true);

    // Shipping
    $table->decimal('weight', 8, 2)->nullable();
    $table->string('weight_unit')->default('g');
    $table->decimal('length', 8, 2)->nullable();
    $table->decimal('width', 8, 2)->nullable();
    $table->decimal('height', 8, 2)->nullable();
    $table->string('dimension_unit')->default('cm');
});

// Collections table (like Shopify Collections)
Schema::create('collections', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->enum('type', ['manual', 'smart'])->default('manual');
    $table->json('conditions')->nullable(); // For smart collections
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

Schema::create('collection_product', function (Blueprint $table) {
    $table->id();
    $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->integer('sort_order')->default(0);
    $table->timestamps();

    $table->unique(['collection_id', 'product_id']);
});

// Shipping zones & rates
Schema::create('shipping_zones', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->json('countries'); // Array of country codes
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

Schema::create('shipping_rates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->enum('type', ['flat', 'weight_based', 'price_based']);
    $table->decimal('rate', 10, 2);
    $table->decimal('min_value', 10, 2)->nullable();
    $table->decimal('max_value', 10, 2)->nullable();
    $table->timestamps();
});

// Customer segments
Schema::create('customer_segments', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->json('conditions'); // Filtering conditions
    $table->timestamps();
});

// Activity log (audit trail)
Schema::create('activity_log', function (Blueprint $table) {
    $table->id();
    $table->string('log_name')->nullable();
    $table->text('description');
    $table->nullableMorphs('subject');
    $table->nullableMorphs('causer');
    $table->json('properties')->nullable();
    $table->timestamps();

    $table->index('log_name');
});
```

#### **1.3 Seeders (Industry-Standard Demo Data)**

```php
// database/seeders/TenantDatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CatalogSeeder::class,         // NEW: 3 catalogs (B2B, B2C, Regional)
            CategorySeeder::class,
            BrandSeeder::class,
            AttributeSeeder::class,
            ProductSeeder::class,        // 50 demo products (distributed across catalogs)
            CustomerSeeder::class,        // 100 demo customers
            OrderSeeder::class,           // 200 demo orders
            CollectionSeeder::class,      // 5 collections
            ShippingZoneSeeder::class,    // 3 shipping zones
            CouponSeeder::class,          // 10 coupons
            FlashSaleSeeder::class,       // 2 flash sales
        ]);
    }
}

// database/seeders/CatalogSeeder.php
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalogs = [
            [
                'name' => 'B2C Retail Catalog',
                'slug' => 'b2c-retail',
                'description' => 'Consumer-facing catalog with standard retail pricing',
                'type' => 'b2c',
                'target_audience' => 'consumers',
                'region_codes' => null,
                'currency' => 'BDT',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
                'settings' => [
                    'show_compare_prices' => true,
                    'allow_reviews' => true,
                    'show_stock_levels' => true,
                ],
            ],
            [
                'name' => 'B2B Wholesale Catalog',
                'slug' => 'b2b-wholesale',
                'description' => 'Business-to-business catalog with wholesale pricing and bulk discounts',
                'type' => 'b2b',
                'target_audience' => 'businesses',
                'region_codes' => null,
                'currency' => 'BDT',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
                'settings' => [
                    'minimum_order_quantity' => 10,
                    'bulk_pricing' => true,
                    'require_account' => true,
                    'show_compare_prices' => false,
                ],
                'pricing_rules' => [
                    'discount_percentage' => 20,
                    'tiered_pricing' => [
                        ['min_qty' => 10, 'discount' => 15],
                        ['min_qty' => 50, 'discount' => 25],
                        ['min_qty' => 100, 'discount' => 30],
                    ],
                ],
            ],
            [
                'name' => 'US Regional Catalog',
                'slug' => 'us-regional',
                'description' => 'Region-specific catalog for United States market',
                'type' => 'regional',
                'target_audience' => 'both',
                'region_codes' => ['US'],
                'currency' => 'USD',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
                'settings' => [
                    'show_shipping_estimates' => true,
                    'region_specific_products' => true,
                ],
                'pricing_rules' => [
                    'currency_conversion' => true,
                    'regional_tax' => true,
                ],
            ],
        ];

        foreach ($catalogs as $catalogData) {
            Catalog::create($catalogData);
        }
    }
}

// database/seeders/CategorySeeder.php
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $catalogs = Catalog::all();

        $categories = [
            ['name' => 'Toys', 'icon' => '🧸', 'children' => [
                ['name' => 'Action Figures', 'icon' => '🦸'],
                ['name' => 'Dolls', 'icon' => '🪆'],
                ['name' => 'Building Blocks', 'icon' => '🧱'],
            ]],
            ['name' => 'Games', 'icon' => '🎮', 'children' => [
                ['name' => 'Board Games', 'icon' => '♟️'],
                ['name' => 'Puzzles', 'icon' => '🧩'],
            ]],
            ['name' => 'Educational', 'icon' => '📚', 'children' => [
                ['name' => 'STEM Toys', 'icon' => '🔬'],
                ['name' => 'Art & Craft', 'icon' => '🎨'],
            ]],
        ];

        // Create categories for each catalog
        foreach ($catalogs as $catalog) {
            foreach ($categories as $categoryData) {
                $category = Category::create([
                    'catalog_id' => $catalog->id,
                    'name' => $categoryData['name'],
                    'slug' => Str::slug($categoryData['name']),
                    'icon' => $categoryData['icon'],
                    'is_active' => true,
                    'show_on_home' => true,
                ]);

                if (isset($categoryData['children'])) {
                    foreach ($categoryData['children'] as $childData) {
                        Category::create([
                            'catalog_id' => $catalog->id,
                            'name' => $childData['name'],
                            'slug' => Str::slug($childData['name']),
                            'icon' => $childData['icon'],
                            'parent_id' => $category->id,
                            'is_active' => true,
                        ]);
                    }
                }
            }
        }
    }
}

// database/seeders/ProductSeeder.php
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $catalogs = Catalog::all();
        $brands = Brand::all();

        // Create products distributed across all catalogs
        foreach ($catalogs as $catalog) {
            $categories = Category::where('catalog_id', $catalog->id)
                ->whereNotNull('parent_id')
                ->get();

            // Create 20 products per catalog
            for ($i = 1; $i <= 20; $i++) {
                $basePrice = $faker->randomFloat(2, 500, 5000);

                // Apply catalog-specific pricing
                $price = $basePrice;
                if ($catalog->type === 'b2b' && isset($catalog->pricing_rules['discount_percentage'])) {
                    $price = $basePrice * (1 - $catalog->pricing_rules['discount_percentage'] / 100);
                }

                Product::create([
                    'catalog_id' => $catalog->id,
                    'name' => $faker->words(3, true),
                    'slug' => Str::slug($faker->words(3, true)),
                    'short_description' => $faker->sentence(),
                    'description' => $faker->paragraphs(3, true),
                    'sku' => 'CAT' . $catalog->id . '-TOY-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'price' => $price,
                    'cost_price' => $faker->randomFloat(2, 300, 3000),
                    'compare_at_price' => $faker->optional(0.3)->randomFloat(2, $price * 1.2, $price * 1.5),
                    'stock_quantity' => $faker->numberBetween(0, 100),
                    'product_type' => 'simple',
                    'is_active' => true,
                    'is_featured' => $faker->boolean(20),
                    'category_id' => $categories->random()->id,
                    'brand_id' => $brands->random()->id,
                    'images' => ['products/demo-' . $i . '.jpg'],
                    'tags' => $faker->words(3),
                    'weight' => $faker->randomFloat(2, 100, 2000),
                    'meta_title' => $faker->sentence(),
                    'meta_description' => $faker->sentence(20),
                ]);
            }
        }
    }
}
```

#### **1.4 Caching Layer Setup**

```php
// config/cache.php - Configure Redis
'default' => env('CACHE_DRIVER', 'redis'),

'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],

// app/Support/Cache/CacheKeys.php
namespace App\Support\Cache;

class CacheKeys
{
    public static function categories(): string
    {
        return 'categories:all';
    }

    public static function categoryTree(): string
    {
        return 'categories:tree';
    }

    public static function brands(): string
    {
        return 'brands:all';
    }

    public static function product(int|string $id): string
    {
        return "product:{$id}";
    }

    public static function homePageData(): string
    {
        return 'homepage:data';
    }

    public static function catalogFilters(): string
    {
        return 'catalog:filters';
    }
}

// Usage in controllers/services
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    public function getAllCategories()
    {
        return Cache::remember(
            CacheKeys::categories(),
            now()->addDay(),
            fn() => Category::with('children')->whereNull('parent_id')->get()
        );
    }

    public function clearCache(): void
    {
        Cache::forget(CacheKeys::categories());
        Cache::forget(CacheKeys::categoryTree());
    }
}
```

#### **Tasks - Phase 1**

| Task | Estimated Time | Priority |
|------|----------------|----------|
| Install tenancy package + configure | 4h | Critical |
| Create central database migrations (tenants, domains) | 3h | Critical |
| **Create catalogs table migration (B2B/B2C/Regional)** | 3h | Critical |
| Refactor all migrations for tenant databases | 8h | Critical |
| **Update products table with catalog_id foreign key** | 2h | Critical |
| **Update categories table with catalog_id foreign key** | 2h | Critical |
| Add missing fields to products table | 2h | High |
| Create collections, shipping zones, segments tables | 4h | High |
| **Create Catalog model with relationships & scopes** | 3h | Critical |
| **Create CatalogSeeder (B2B, B2C, Regional catalogs)** | 4h | Critical |
| **Update ProductSeeder to distribute products across catalogs** | 3h | High |
| **Update CategorySeeder to create categories per catalog** | 2h | High |
| Create comprehensive seeders (customers, orders) | 4h | High |
| Set up Redis caching layer | 3h | High |
| Implement cache keys system (+ catalog caching) | 2h | Medium |
| Create Tenant model with helper methods | 2h | Critical |
| **Test catalog isolation (products scoped per catalog)** | 3h | Critical |
| Test multi-tenancy (create 3 test tenants) | 4h | Critical |
| Database indexes optimization | 3h | High |
| Activity log setup (Spatie Activity Log) | 2h | Medium |

**Total Phase 1:** 59 hours (~2.5 weeks)

---

### **PHASE 2: Livewire 4 Migration - Storefront (Weeks 4-5)**

#### **2.1 Install Livewire 4**

```bash
composer require livewire/livewire:^3.0
php artisan livewire:publish --config
php artisan livewire:publish --assets
```

#### **2.2 Create Base Livewire Components**

**App Layout with Livewire**
```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-nunito antialiased">
    @livewire('storefront.navigation')

    <main>
        {{ $slot }}
    </main>

    @livewire('storefront.footer')
    @livewire('storefront.cart-drawer')

    @livewireScripts
</body>
</html>
```

**Navigation Component (Livewire)**
```php
// app/Livewire/Storefront/Navigation.php
namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\On;

class Navigation extends Component
{
    public $cartCount = 0;
    public $searchQuery = '';

    public function mount()
    {
        $this->updateCartCount();
    }

    #[On('cart-updated')]
    public function updateCartCount()
    {
        $cart = session('cart', []);
        $this->cartCount = count($cart['items'] ?? []);
    }

    public function search()
    {
        if (empty($this->searchQuery)) return;

        return redirect()->route('search', ['q' => $this->searchQuery]);
    }

    public function render()
    {
        return view('livewire.storefront.navigation', [
            'categories' => cache()->remember('nav-categories', 3600,
                fn() => Category::whereNull('parent_id')
                    ->with('children')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
            ),
        ]);
    }
}
```

#### **2.3 Convert Key Pages to Livewire**

**Product Catalog (Full-Page Component)**
```php
// app/Livewire/Storefront/ProductCatalog.php
namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Domain\Product\Models\Product;
use App\Domain\Product\Models\Category;
use App\Domain\Product\Models\Brand;
use App\Domain\Catalog\Models\Catalog;

class ProductCatalog extends Component
{
    use WithPagination;

    #[Url] public $catalog = null; // NEW: Catalog filter
    #[Url] public $category = null;
    #[Url] public $brand = null;
    #[Url] public $priceRange = null;
    #[Url] public $sort = 'newest';
    #[Url] public $search = '';

    public $filters = [
        'featured' => false,
        'new' => false,
        'sale' => false,
    ];

    public $activeCatalog; // Current active catalog object

    public function mount()
    {
        // Load active catalog from session or use default
        $catalogId = $this->catalog ?? session('active_catalog_id');

        if (!$catalogId) {
            // Use default catalog
            $this->activeCatalog = Catalog::where('is_default', true)->first();
        } else {
            $this->activeCatalog = Catalog::find($catalogId);
        }

        if ($this->activeCatalog) {
            $this->catalog = $this->activeCatalog->id;
            session(['active_catalog_id' => $this->activeCatalog->id]);
        }
    }

    public function switchCatalog($catalogId)
    {
        $this->catalog = $catalogId;
        $this->activeCatalog = Catalog::findOrFail($catalogId);
        session(['active_catalog_id' => $catalogId]);
        $this->reset(['category', 'brand', 'priceRange']);
        $this->resetPage();

        $this->dispatch('catalog-switched', catalogId: $catalogId);
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedBrand()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['category', 'brand', 'priceRange', 'filters']);
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        // Add to cart logic
        $cart = session('cart', ['items' => []]);
        $cart['items'][$productId] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'image' => $product->primary_image,
            'catalog_id' => $product->catalog_id,
        ];
        session(['cart' => $cart]);

        $this->dispatch('cart-updated');
        $this->dispatch('show-notification', message: 'Product added to cart!');
    }

    public function render()
    {
        $query = Product::query()->where('is_active', true);

        // Filter by active catalog (CRITICAL)
        if ($this->activeCatalog) {
            $query->where('catalog_id', $this->activeCatalog->id);
        }

        // Apply filters
        if ($this->category) {
            $query->where('category_id', $this->category);
        }

        if ($this->brand) {
            $query->where('brand_id', $this->brand);
        }

        if ($this->priceRange) {
            [$min, $max] = explode('-', $this->priceRange);
            $query->whereBetween('price', [(int)$min, (int)$max]);
        }

        if ($this->filters['featured']) {
            $query->where('is_featured', true);
        }

        if ($this->filters['new']) {
            $query->where('created_at', '>=', now()->subDays(14));
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        // Sorting
        match($this->sort) {
            'price-low' => $query->orderBy('price', 'asc'),
            'price-high' => $query->orderBy('price', 'desc'),
            'popular' => $query->orderBy('sold_count', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        return view('livewire.storefront.product-catalog', [
            'products' => $query->with(['category', 'brand', 'catalog'])->paginate(12),
            'categories' => Category::where('catalog_id', $this->activeCatalog?->id)
                ->where('is_active', true)
                ->get(),
            'brands' => Brand::where('is_active', true)->get(),
            'catalogs' => Catalog::active()->orderBy('sort_order')->get(), // All available catalogs
        ]);
    }
}
```

**Shopping Cart (Drawer Component)**
```php
// app/Livewire/Storefront/CartDrawer.php
namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\On;

class CartDrawer extends Component
{
    public $isOpen = false;
    public $items = [];
    public $total = 0;

    public function mount()
    {
        $this->loadCart();
    }

    #[On('cart-updated')]
    public function loadCart()
    {
        $cart = session('cart', ['items' => []]);
        $this->items = $cart['items'];
        $this->total = array_sum(array_map(
            fn($item) => $item['price'] * $item['quantity'],
            $this->items
        ));
    }

    public function updateQuantity($productId, $quantity)
    {
        $cart = session('cart', ['items' => []]);

        if ($quantity <= 0) {
            unset($cart['items'][$productId]);
        } else {
            $cart['items'][$productId]['quantity'] = $quantity;
        }

        session(['cart' => $cart]);
        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function removeItem($productId)
    {
        $this->updateQuantity($productId, 0);
    }

    public function toggleDrawer()
    {
        $this->isOpen = !$this->isOpen;
    }

    #[On('open-cart')]
    public function openDrawer()
    {
        $this->isOpen = true;
    }

    public function render()
    {
        return view('livewire.storefront.cart-drawer');
    }
}
```

**Product Quick View (Modal)**
```php
// app/Livewire/Storefront/ProductQuickView.php
namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Domain\Product\Models\Product;

class ProductQuickView extends Component
{
    public $isOpen = false;
    public $product = null;
    public $selectedVariant = null;
    public $quantity = 1;

    #[On('open-quick-view')]
    public function open($productId)
    {
        $this->product = Product::with(['variants', 'category', 'brand'])
            ->findOrFail($productId);
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->reset(['product', 'selectedVariant', 'quantity']);
    }

    public function addToCart()
    {
        // Add to cart logic
        $this->dispatch('cart-updated');
        $this->dispatch('show-notification', message: 'Added to cart!');
        $this->close();
    }

    public function render()
    {
        return view('livewire.storefront.product-quick-view');
    }
}
```

**Search Autocomplete**
```php
// app/Livewire/Storefront/SearchAutocomplete.php
namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domain\Product\Models\Product;

class SearchAutocomplete extends Component
{
    public $query = '';
    public $results = [];
    public $isOpen = false;

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->isOpen = false;
            return;
        }

        $this->results = Product::where('is_active', true)
            ->where('name', 'like', "%{$this->query}%")
            ->limit(5)
            ->get(['id', 'name', 'slug', 'price', 'primary_image']);

        $this->isOpen = true;
    }

    public function selectProduct($slug)
    {
        return redirect()->route('product.show', $slug);
    }

    public function render()
    {
        return view('livewire.storefront.search-autocomplete');
    }
}
```

#### **2.4 Livewire Views (Blade Templates)**

```blade
{{-- resources/views/livewire/storefront/product-catalog.blade.php --}}
<div class="container mx-auto px-4 py-8">
    {{-- Mobile Filters --}}
    <div class="lg:hidden mb-4">
        <button wire:click="$toggle('showMobileFilters')" class="btn-primary w-full">
            Filters
        </button>
    </div>

    <div class="grid lg:grid-cols-12 gap-8">
        {{-- Sidebar Filters (Desktop) --}}
        <aside class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold mb-4">Categories</h3>
                @foreach($categories as $category)
                    <label class="flex items-center gap-2 mb-2">
                        <input type="radio" wire:model.live="category" value="{{ $category->id }}">
                        <span>{{ $category->name }}</span>
                    </label>
                @endforeach

                <h3 class="font-bold mt-6 mb-4">Brands</h3>
                @foreach($brands as $brand)
                    <label class="flex items-center gap-2 mb-2">
                        <input type="radio" wire:model.live="brand" value="{{ $brand->id }}">
                        <span>{{ $brand->name }}</span>
                    </label>
                @endforeach

                <button wire:click="clearFilters" class="btn-secondary w-full mt-4">
                    Clear Filters
                </button>
            </div>
        </aside>

        {{-- Product Grid --}}
        <main class="lg:col-span-9">
            <div class="flex justify-between items-center mb-6">
                <p class="text-gray-600">{{ $products->total() }} products</p>

                <select wire:model.live="sort" class="form-select">
                    <option value="newest">Newest</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="popular">Most Popular</option>
                </select>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <article class="bg-white rounded-lg shadow overflow-hidden group">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <img src="{{ asset($product->primary_image) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full aspect-square object-cover group-hover:scale-110 transition">
                        </a>
                        <div class="p-4">
                            <h3 class="font-semibold line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-primary font-bold mt-2">৳{{ number_format($product->price, 2) }}</p>

                            <button wire:click="addToCart({{ $product->id }})"
                                    class="btn-primary w-full mt-4">
                                Add to Cart
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </main>
    </div>
</div>
```

#### **Tasks - Phase 2**

| Task | Estimated Time | Priority |
|------|----------------|----------|
| Install Livewire 4 + configure | 2h | Critical |
| Create base layouts with Livewire | 4h | Critical |
| Convert Navigation to Livewire component | 3h | High |
| **Create Catalog Switcher component (Livewire)** | 5h | Critical |
| **Update Product Catalog with catalog filtering** | 10h | Critical |
| Create Shopping Cart Drawer (Livewire) | 6h | Critical |
| Create Product Quick View modal | 4h | High |
| **Update Search Autocomplete with catalog scope** | 5h | High |
| Convert Product Details page to Livewire | 6h | High |
| Convert Checkout to Livewire (multi-step) | 8h | Critical |
| Create Wishlist component (Livewire) | 4h | Medium |
| Create Recently Viewed component | 3h | Medium |
| Create Product Comparison component | 6h | Medium |
| **Test catalog switching & product isolation** | 4h | Critical |
| Test all Livewire components | 6h | Critical |
| Mobile responsiveness testing | 4h | High |

**Total Phase 2:** 80 hours (~2.5 weeks with focus)

---

### **PHASE 3: Filament Admin Panel (Weeks 6-8)**

Filament is a modern admin panel framework for Laravel that provides:
- ✅ Automatic CRUD interfaces
- ✅ Advanced table builder with filters, sorting, bulk actions
- ✅ Form builder with validation
- ✅ Dashboard widgets
- ✅ Multi-tenant support built-in
- ✅ Role-based permissions
- ✅ Dark mode
- ✅ Extensible architecture

#### **3.1 Install Filament**

```bash
composer require filament/filament:^3.0
php artisan filament:install --panels
```

#### **3.2 Configure Filament for Multi-Tenancy**

```php
// config/filament.php
return [
    'tenancy' => [
        'enabled' => true,
        'tenant_model' => \App\Domain\Tenant\Models\Tenant::class,
        'tenant_slug_attribute' => 'subdomain',
    ],
];
```

#### **3.3 Create Filament Resources**

**Product Resource**
```php
// app/Filament/Resources/ProductResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Domain\Product\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catalog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) =>
                                        $set('slug', Str::slug($state))
                                    ),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('sku')
                                    ->unique(ignoreRecord: true),

                                Forms\Components\RichEditor::make('description')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Pricing')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->required()
                                    ->prefix('৳'),

                                Forms\Components\TextInput::make('cost_price')
                                    ->numeric()
                                    ->prefix('৳'),

                                Forms\Components\TextInput::make('compare_at_price')
                                    ->numeric()
                                    ->prefix('৳'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Inventory')
                            ->schema([
                                Forms\Components\TextInput::make('stock_quantity')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\Toggle::make('track_inventory')
                                    ->default(true),

                                Forms\Components\TextInput::make('low_stock_threshold')
                                    ->numeric()
                                    ->default(10),
                            ]),

                        Forms\Components\Tabs\Tab::make('Images')
                            ->schema([
                                Forms\Components\FileUpload::make('primary_image')
                                    ->image()
                                    ->directory('products')
                                    ->disk('public'),

                                Forms\Components\FileUpload::make('images')
                                    ->image()
                                    ->multiple()
                                    ->directory('products')
                                    ->disk('public')
                                    ->reorderable(),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(60),

                                Forms\Components\Textarea::make('meta_description')
                                    ->maxLength(160),

                                Forms\Components\TagsInput::make('meta_keywords'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),

                        Forms\Components\Toggle::make('is_featured'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primary_image')
                    ->label('Image'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->money('BDT')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->sortable()
                    ->color(fn ($record) => $record->stock_quantity <= $record->low_stock_threshold ? 'danger' : 'success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),

                Tables\Filters\SelectFilter::make('brand')
                    ->relationship('brand', 'name'),

                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_featured'),

                Tables\Filters\Filter::make('low_stock')
                    ->query(fn ($query) => $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (Product $record) {
                        $newProduct = $record->replicate();
                        $newProduct->name .= ' (Copy)';
                        $newProduct->slug .= '-copy';
                        $newProduct->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
```

**Order Resource (with inline editing, status updates, invoices)**
```php
// app/Filament/Resources/OrderResource.php
class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable(),

                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->rules(['required']),

                Tables\Columns\TextColumn::make('total')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Order $record) => route('admin.orders.invoice', $record)),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export')
                    ->action(function ($records) {
                        return response()->download(
                            Excel::download(new OrdersExport($records), 'orders.csv')
                        );
                    }),
            ]);
    }
}
```

**Dashboard Widgets**
```php
// app/Filament/Widgets/StatsOverview.php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Domain\Order\Models\Order;
use App\Domain\Customer\Models\Customer;
use App\Domain\Product\Models\Product;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $revenue = Order::where('status', '!=', 'cancelled')
            ->sum('total');

        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->count();

        $customers = Customer::count();

        $lowStock = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();

        return [
            Stat::make('Total Revenue', '৳' . number_format($revenue, 2))
                ->description('All time revenue')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Orders This Month', $ordersThisMonth)
                ->description('Current month orders')
                ->color('info'),

            Stat::make('Total Customers', $customers)
                ->description('Registered customers')
                ->color('primary'),

            Stat::make('Low Stock Alerts', $lowStock)
                ->description('Products need restocking')
                ->color($lowStock > 0 ? 'danger' : 'success'),
        ];
    }
}
```

#### **Tasks - Phase 3**

| Task | Estimated Time | Priority |
|------|----------------|----------|
| Install Filament 3 + configure | 3h | Critical |
| Configure multi-tenancy in Filament | 4h | Critical |
| **Create Catalog Resource (CRUD + product assignment)** | 6h | Critical |
| **Update Product Resource with catalog selection** | 9h | Critical |
| Create Order Resource (with inline status) | 6h | Critical |
| **Update Category Resource with catalog filtering** | 5h | High |
| Create Brand Resource | 3h | High |
| Create Customer Resource | 5h | High |
| Create Collection Resource | 4h | Medium |
| Create Coupon Resource | 4h | Medium |
| **Update Dashboard Widgets (include catalog metrics)** | 7h | High |
| Create Reports (sales, inventory, customers) | 8h | Medium |
| Implement activity log in Filament | 4h | Medium |
| Create Settings page (Filament) | 5h | Medium |
| Implement bulk import/export | 6h | Medium |
| **Test catalog-based filtering in all resources** | 4h | Critical |
| Test all Filament resources | 6h | Critical |

**Total Phase 3:** 89 hours (~3.5 weeks)

---

### **PHASE 4: API & Integrations (Weeks 9-10)**

#### **4.1 Laravel Sanctum API**

```php
// routes/api.php
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\CustomerController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Products
    Route::apiResource('products', ProductController::class);
    Route::get('products/{product}/variants', [ProductController::class, 'variants']);

    // Orders
    Route::apiResource('orders', OrderController::class);
    Route::post('orders/{order}/fulfill', [OrderController::class, 'fulfill']);

    // Customers
    Route::apiResource('customers', CustomerController::class);
    Route::get('customers/{customer}/orders', [CustomerController::class, 'orders']);
});

// Webhooks
Route::post('webhooks/{tenant}/stripe', [StripeWebhookController::class, 'handle']);
```

#### **4.2 Webhook System**

```php
// app/Domain/Webhook/Models/Webhook.php
class Webhook extends Model
{
    protected $fillable = ['url', 'events', 'secret', 'is_active'];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
    ];

    public function send(string $event, array $payload): void
    {
        if (!$this->is_active || !in_array($event, $this->events)) {
            return;
        }

        Http::withHeaders([
            'X-Webhook-Signature' => hash_hmac('sha256', json_encode($payload), $this->secret),
        ])->post($this->url, $payload);
    }
}

// Usage
event(new OrderCreated($order));

// app/Listeners/SendOrderCreatedWebhook.php
class SendOrderCreatedWebhook
{
    public function handle(OrderCreated $event): void
    {
        $webhooks = Webhook::where('is_active', true)->get();

        foreach ($webhooks as $webhook) {
            $webhook->send('order.created', [
                'event' => 'order.created',
                'data' => $event->order->toArray(),
                'timestamp' => now()->toIso8601String(),
            ]);
        }
    }
}
```

#### **Tasks - Phase 4**

| Task | Estimated Time | Priority |
|------|----------------|----------|
| Set up Laravel Sanctum | 2h | Critical |
| Create API controllers (Products, Orders, Customers) | 8h | High |
| Implement API rate limiting | 2h | High |
| Create API documentation (OpenAPI/Swagger) | 6h | Medium |
| Create Webhook model + delivery system | 6h | High |
| Integrate Stripe payment gateway | 8h | Critical |
| Integrate PayPal payment gateway | 6h | Medium |
| Create shipping carrier integrations | 8h | Medium |
| Test API endpoints | 4h | High |
| Test webhooks | 3h | High |

**Total Phase 4:** 53 hours (~2 weeks)

---

### **PHASE 5: SaaS Features (Weeks 11-12)**

#### **5.1 Tenant Onboarding**

```php
// app/Livewire/Onboarding/TenantRegistration.php
class TenantRegistration extends Component
{
    public $step = 1;
    public $name;
    public $subdomain;
    public $email;
    public $password;
    public $plan = 'starter';

    protected $rules = [
        'name' => 'required|min:3',
        'subdomain' => 'required|alpha_dash|unique:tenants,subdomain',
        'email' => 'required|email|unique:tenants,email',
        'password' => 'required|min:8',
        'plan' => 'required|in:starter,professional,enterprise',
    ];

    public function nextStep()
    {
        $this->validateOnly([
            1 => ['name', 'subdomain'],
            2 => ['email', 'password'],
            3 => ['plan'],
        ][$this->step]);

        $this->step++;
    }

    public function register()
    {
        $this->validate();

        DB::transaction(function () {
            $tenant = Tenant::create([
                'name' => $this->name,
                'subdomain' => $this->subdomain,
                'email' => $this->email,
                'plan' => $this->plan,
                'trial_ends_at' => now()->addDays(14),
            ]);

            $tenant->domains()->create([
                'domain' => $this->subdomain . '.example.com',
                'is_primary' => true,
            ]);

            // Create tenant database
            $tenant->createDatabase();
            $tenant->migrate();

            // Seed with demo data
            $tenant->run(function () {
                Artisan::call('db:seed', ['--class' => 'TenantDatabaseSeeder']);
            });

            // Create admin user
            $tenant->run(function () use ($tenant) {
                User::create([
                    'name' => $tenant->name,
                    'email' => $tenant->email,
                    'password' => bcrypt($this->password),
                    'role' => 'admin',
                ]);
            });
        });

        session()->flash('success', 'Tenant created successfully!');
        return redirect('https://' . $this->subdomain . '.example.com/admin');
    }
}
```

#### **5.2 Subscription Billing (Laravel Cashier)**

```php
// app/Domain/Tenant/Models/Tenant.php
use Laravel\Cashier\Billable;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use Billable;

    public function subscribeToStarterPlan()
    {
        return $this->newSubscription('default', 'price_starter_monthly')
            ->create();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscribed('default');
    }
}

// Billing portal
Route::get('/billing', function () {
    return redirect(auth()->user()->tenant->billingPortalUrl(route('settings')));
});
```

#### **Tasks - Phase 5**

| Task | Estimated Time | Priority |
|------|----------------|----------|
| Create tenant registration Livewire component | 8h | Critical |
| Implement tenant onboarding flow (3 steps) | 6h | Critical |
| Set up Laravel Cashier (Stripe) | 4h | Critical |
| Create subscription plans (Starter, Pro, Enterprise) | 4h | Critical |
| Create billing portal | 4h | High |
| Implement usage-based billing | 8h | Medium |
| Create trial management system | 4h | High |
| Implement tenant suspension/cancellation | 4h | Medium |
| Create super-admin dashboard (manage all tenants) | 8h | High |
| Test onboarding flow | 4h | Critical |

**Total Phase 5:** 54 hours (~2 weeks)

---

### **PHASE 6: Performance & Scale (Weeks 13-14)**

#### **6.1 Caching Strategy**

```php
// Cache all categories
Cache::remember('categories', 86400, fn() =>
    Category::with('children')->whereNull('parent_id')->get()
);

// Cache homepage data
Cache::remember('homepage', 3600, fn() => [
    'featured_products' => Product::where('is_featured', true)->limit(8)->get(),
    'new_arrivals' => Product::latest()->limit(8)->get(),
    'flash_sales' => FlashSale::active()->with('products')->first(),
]);

// Cache product details
Cache::remember("product:{$slug}", 3600, fn() =>
    Product::with(['variants', 'category', 'brand'])->where('slug', $slug)->first()
);
```

#### **6.2 Queue Jobs**

```php
// app/Jobs/SendOrderConfirmationEmail.php
class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        Mail::to($this->order->customer->email)
            ->send(new OrderConfirmation($this->order));
    }
}

// Usage
dispatch(new SendOrderConfirmationEmail($order));
```

#### **6.3 CDN Setup**

```php
// config/filesystems.php
'disks' => [
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
    ],
],

// Use CDN for images
Storage::disk('s3')->put('products/' . $filename, $file);
$url = Storage::disk('s3')->url('products/' . $filename);
```

#### **Tasks - Phase 6**

| Task | Estimated Time | Priority |
|------|----------------|----------|
| Implement comprehensive caching strategy | 8h | Critical |
| Set up Laravel Horizon (queue monitoring) | 4h | High |
| Configure AWS S3 for media storage | 4h | High |
| Set up CloudFlare CDN | 4h | High |
| Implement image optimization (WebP) | 6h | Medium |
| Add database query optimization | 6h | High |
| Set up Elasticsearch for search | 12h | Medium |
| Implement full-page caching | 4h | Medium |
| Load testing (K6/Artillery) | 6h | High |
| Performance monitoring (Laravel Telescope) | 3h | High |

**Total Phase 6:** 57 hours (~2 weeks)

---

### **PHASE 7: Testing & Deployment (Weeks 15-16)**

#### **7.1 Testing Strategy**

```php
// tests/Feature/Livewire/ProductCatalogTest.php
use Livewire\Livewire;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_filter_products_by_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        Livewire::test(ProductCatalog::class)
            ->set('category', $category->id)
            ->assertSee($product->name);
    }

    /** @test */
    public function it_can_add_product_to_cart()
    {
        $product = Product::factory()->create();

        Livewire::test(ProductCatalog::class)
            ->call('addToCart', $product->id)
            ->assertDispatched('cart-updated');

        $this->assertArrayHasKey($product->id, session('cart')['items']);
    }
}
```

#### **Tasks - Phase 7**

| Task | Estimated Time | Priority |
|------|----------------|----------|
| Write unit tests (models, services) | 16h | Critical |
| Write feature tests (Livewire components) | 16h | Critical |
| Write API tests | 8h | High |
| Browser testing (Dusk/Playwright) | 12h | Medium |
| Set up CI/CD (GitHub Actions) | 6h | High |
| Deploy to staging server | 8h | Critical |
| UAT (User Acceptance Testing) | 12h | Critical |
| Deploy to production | 8h | Critical |
| Post-deployment monitoring | 4h | High |
| Documentation (user guide, API docs) | 10h | Medium |

**Total Phase 7:** 100 hours (~2.5 weeks)

---

## 7️⃣ **DATABASE SCHEMA CHANGES SUMMARY**

### **Central Database (central)**

```sql
-- Tenants management
tenants (id, name, subdomain, database, email, status, plan, trial_ends_at, settings, created_at, updated_at)
domains (id, tenant_id, domain, is_primary, created_at, updated_at)

-- Super admin users
admins (id, name, email, password, created_at, updated_at)

-- Subscriptions (Laravel Cashier)
subscriptions (id, user_id, name, stripe_id, stripe_status, stripe_price, quantity, trial_ends_at, ends_at, created_at, updated_at)
subscription_items (id, subscription_id, stripe_id, stripe_product, stripe_price, quantity, created_at, updated_at)
```

### **Tenant Database (tenant_xxx)**

All existing tables PLUS:

```sql
-- NEW: Catalogs table (B2B/B2C/Regional product sets)
catalogs (
    id, name, slug, description, type, target_audience, region_codes,
    currency, language, is_active, is_default, sort_order, settings,
    pricing_rules, created_at, updated_at, deleted_at
)

-- New tables
collections (id, name, slug, description, image, type, conditions, sort_order, is_active)
collection_product (id, collection_id, product_id, sort_order)

shipping_zones (id, name, countries, is_active)
shipping_rates (id, shipping_zone_id, name, type, rate, min_value, max_value)

customer_segments (id, name, description, conditions)

webhooks (id, url, events, secret, is_active, created_at, updated_at)

activity_log (id, log_name, description, subject_type, subject_id, causer_type, causer_id, properties, created_at)

-- Enhanced tables (new columns)
products: + (catalog_id [FK], meta_title, meta_description, sold_count, average_rating, review_count, published_at, low_stock_threshold, track_inventory, weight, length, width, height)
    UNIQUE constraints: (catalog_id, slug), (catalog_id, sku)

categories: + (catalog_id [FK])
    UNIQUE constraints: (catalog_id, slug)

orders: + (tracking_number, fulfillment_status, notes, shipping_address_json)

customers: + (segment_id, lifetime_value, total_orders, average_order_value)
```

### **Data Hierarchy (Tenant → Catalog → Category → Product)**

```
Tenant (Shop)
├── Catalog: B2C Retail (Default)
│   ├── Category: Toys
│   │   ├── Product: Action Figure A
│   │   └── Product: Doll B
│   └── Category: Games
│       └── Product: Board Game C
├── Catalog: B2B Wholesale
│   ├── Category: Toys (wholesale pricing)
│   │   └── Product: Action Figure A (20% discount)
│   └── Category: Games (bulk pricing)
│       └── Product: Board Game C (tiered pricing)
└── Catalog: US Regional
    ├── Category: Toys (USD pricing)
    │   └── Product: Action Figure A (US market)
    └── Category: Games
        └── Product: Board Game C (US compliant)
```

---

## 8️⃣ **CODE STRUCTURE & ORGANIZATION**

### **Final Project Structure**

```
kiddo's-heaven/
├── app/
│   ├── Domain/                          # Domain-Driven Design
│   │   ├── Product/
│   │   │   ├── Models/
│   │   │   ├── Actions/
│   │   │   ├── QueryBuilders/
│   │   │   ├── Collections/
│   │   │   └── Events/
│   │   ├── Order/
│   │   ├── Customer/
│   │   ├── Inventory/
│   │   ├── Tenant/
│   │   └── Shared/
│   ├── Livewire/                        # Livewire Components
│   │   ├── Storefront/
│   │   │   ├── ProductCatalog.php
│   │   │   ├── ProductDetails.php
│   │   │   ├── ShoppingCart.php
│   │   │   ├── CartDrawer.php
│   │   │   ├── Checkout.php
│   │   │   ├── SearchAutocomplete.php
│   │   │   └── Navigation.php
│   │   ├── Admin/
│   │   └── Shared/
│   ├── Filament/                        # Filament Admin
│   │   ├── Resources/
│   │   │   ├── CatalogResource.php      # NEW: Manage catalogs
│   │   │   ├── ProductResource.php      # Updated with catalog selection
│   │   │   ├── CategoryResource.php     # Updated with catalog filtering
│   │   │   ├── OrderResource.php
│   │   │   └── CustomerResource.php
│   │   ├── Pages/
│   │   └── Widgets/
│   │       └── CatalogStatsWidget.php   # NEW: Catalog metrics
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/V1/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Jobs/
│   ├── Listeners/
│   └── Support/
│       ├── Enums/
│       ├── Traits/
│       └── Cache/
├── config/
│   ├── tenancy.php
│   ├── filament.php
│   └── cashier.php
├── database/
│   ├── migrations/
│   │   ├── central/
│   │   └── tenant/
│   └── seeders/
│       ├── CentralDatabaseSeeder.php
│       └── TenantDatabaseSeeder.php
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   │   ├── storefront/
│   │   │   └── shared/
│   │   └── layouts/
│   │       └── app.blade.php
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   ├── api.php
│   ├── tenant.php
│   └── central.php
├── tests/
│   ├── Feature/
│   │   ├── Livewire/
│   │   └── Api/
│   └── Unit/
└── README.md
```

---

## 9️⃣ **TESTING STRATEGY**

### **Test Coverage Goals**

| Type | Coverage | Tools |
|------|----------|-------|
| Unit Tests | 80%+ | PHPUnit |
| Feature Tests | 90%+ | PHPUnit + Livewire Testing |
| Browser Tests | Critical paths | Laravel Dusk |
| API Tests | 100% endpoints | PHPUnit |
| Load Tests | 1000 concurrent users | K6/Artillery |

---

## 🔟 **DEPLOYMENT PLAN**

### **Infrastructure**

```
Production Environment:
- AWS EC2 (t3.medium) × 2 (web servers)
- AWS RDS MySQL 8.0 (db.t3.medium)
- AWS ElastiCache Redis (cache.t3.small)
- AWS S3 (media storage)
- CloudFlare (CDN + DDoS protection)
- Laravel Forge (server management)
```

### **Deployment Steps**

1. **Staging Environment Setup** (Week 15)
2. **Production Environment Setup** (Week 15)
3. **Database Migration (zero-downtime)** (Week 16)
4. **DNS Configuration** (Week 16)
5. **SSL Certificates** (Week 16)
6. **Go Live** (Week 16)

---

## 1️⃣1️⃣ **RISK MITIGATION**

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Data migration issues | Medium | High | Test migrations on staging, full backups before migration |
| Performance degradation | Low | High | Load testing in Phase 6, caching strategy |
| Livewire learning curve | Low | Medium | Follow official docs, use proven patterns |
| Multi-tenancy bugs | Medium | Critical | Comprehensive testing with multiple tenants |
| Third-party API failures | Medium | Medium | Implement retry logic, fallback mechanisms |
| Security vulnerabilities | Low | Critical | Code review, security audit, penetration testing |

---

## 1️⃣2️⃣ **SUCCESS METRICS**

### **Technical Metrics**

- ✅ Page load time: <2s (currently ~4s)
- ✅ API response time: <200ms
- ✅ Database queries per page: <50 (currently 100+)
- ✅ Test coverage: >85%
- ✅ Uptime: 99.9%

### **Business Metrics**

- ✅ Support 1000+ tenants
- ✅ 10k concurrent users
- ✅ 100k products per tenant
- ✅ Sub-2s checkout completion time

---

## 📅 **TIMELINE SUMMARY**

| Phase | Duration | Completion Date |
|-------|----------|-----------------|
| Phase 0: Setup | 1 week | Week 1 |
| Phase 1: Backend Foundation + Catalogs | 2.5 weeks | Week 3.5 |
| Phase 2: Livewire Migration + Catalog UI | 2.5 weeks | Week 6 |
| Phase 3: Filament Admin + Catalog Management | 3.5 weeks | Week 9.5 |
| Phase 4: API & Integrations | 2 weeks | Week 11.5 |
| Phase 5: SaaS Features | 2 weeks | Week 13.5 |
| Phase 6: Performance | 2 weeks | Week 15.5 |
| Phase 7: Testing & Deployment | 2 weeks | Week 17.5 |

**Total Duration:** 18 weeks (~4.5 months)

---

## 🚀 **NEXT STEPS**

### **Immediate Actions (This Week)**

1. ✅ Review and approve this plan
2. ✅ Set up development environment
3. ✅ Install required packages
4. ✅ Create Git repository + branching strategy
5. ✅ Begin Phase 0 tasks

### **Questions to Confirm**

1. **Domain structure:** Will it be `{tenant}.yourdomain.com` or custom domains?
2. **Payment gateway:** Stripe only or Stripe + PayPal?
3. **Email service:** AWS SES or alternative?
4. **Hosting preference:** AWS, DigitalOcean, or Laravel Forge?
5. **Budget for infrastructure:** Monthly hosting budget?

---

**Ready to start Phase 0?** Let me know and I'll begin with:
1. Setting up the development environment
2. Installing Tenancy + Livewire + Filament packages
3. Refactoring project structure to Domain-Driven Design
4. Creating central + tenant database migrations
5. **Creating catalogs table migration & Catalog model**
6. **Setting up catalog-based product architecture**

---

## 📦 **CATALOG IMPLEMENTATION STRATEGY**

### **Why Catalogs?**

Based on your requirements for B2B vs B2C product sets and regional product variations, the Catalog entity enables:

1. **B2B vs B2C Separation**
   - Separate product catalogs with different pricing rules
   - B2C: Retail pricing, customer reviews, promotions
   - B2B: Wholesale pricing, bulk discounts, minimum order quantities

2. **Regional Product Variations**
   - US catalog: USD pricing, US-specific products, US compliance
   - EU catalog: EUR pricing, EU-specific products, GDPR compliance
   - Asia catalog: Regional currencies, localized products

3. **Business Model Flexibility**
   - Single tenant can have multiple catalogs
   - Products can exist in multiple catalogs with different prices
   - Categories scoped per catalog for better organization

### **Catalog Architecture**

**Hierarchy:**
```
Tenant (Shop) → Catalog (B2B/B2C/Regional) → Category → Product
```

**Key Features:**
- ✅ Each catalog has its own categories (same category structure per catalog)
- ✅ Products belong to one catalog (avoid duplication)
- ✅ Catalog-specific pricing rules (discounts, tiered pricing)
- ✅ Catalog switcher in storefront (session-based)
- ✅ Catalog filtering in admin panel
- ✅ Default catalog per tenant
- ✅ Active/inactive catalog status

### **Use Cases**

**Use Case 1: Dual B2C + B2B Store**
```
Tenant: "Kiddo's Heaven"
├── Catalog: "Retail Store" (B2C, Default)
│   ├── Price: ৳1,000
│   └── Features: Reviews, Wishlist, Promotions
└── Catalog: "Wholesale Portal" (B2B)
    ├── Price: ৳800 (20% discount)
    └── Features: Bulk pricing, Account required, Net 30 payment terms
```

**Use Case 2: Multi-Region Expansion**
```
Tenant: "Global Toys Inc."
├── Catalog: "Bangladesh Catalog"
│   ├── Currency: BDT
│   └── Products: Local market products
├── Catalog: "USA Catalog"
│   ├── Currency: USD
│   └── Products: US market products (different suppliers)
└── Catalog: "EU Catalog"
    ├── Currency: EUR
    └── Products: EU compliant products
```

### **Implementation Details**

**Database Schema:**
- `catalogs` table: Stores catalog metadata, pricing rules, settings
- `products.catalog_id`: Foreign key linking product to catalog
- `categories.catalog_id`: Foreign key linking category to catalog
- Unique constraints: `(catalog_id, slug)` for products and categories

**Livewire Components:**
- `CatalogSwitcher`: Dropdown/modal to switch between available catalogs
- `ProductCatalog`: Updated to filter products by active catalog
- `SearchAutocomplete`: Scoped to current active catalog

**Filament Resources:**
- `CatalogResource`: Full CRUD for managing catalogs
- `ProductResource`: Catalog selection dropdown
- `CategoryResource`: Filtered by selected catalog

**Session Management:**
- `active_catalog_id` stored in session
- Defaults to tenant's default catalog
- Persists across page navigation

### **Migration Path**

**Current State:**
- No catalogs (all products in single namespace)
- Categories shared across entire tenant

**Target State:**
- Multiple catalogs per tenant
- Products scoped to catalogs
- Categories scoped to catalogs

**Migration Steps:**
1. Create `catalogs` table
2. Create default B2C catalog for existing tenants
3. Add `catalog_id` to products/categories
4. Backfill existing products with default catalog ID
5. Update all queries to include catalog scope

🎯 **Let's build the Shopify competitor with multi-catalog support!**
