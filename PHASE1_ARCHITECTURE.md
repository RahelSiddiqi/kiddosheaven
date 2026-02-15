# PHASE 1: Architecture Restructure, Migration Redesign & Theme Engine Plan

**Status:** AWAITING APPROVAL
**Scope:** Domain restructure, clean migration rewrite, theme engine architecture
**Rule:** No code generated until approved

---

## CURRENT STATE AUDIT

### Codebase Analysis Summary

**65+ migration files** - heavily patched, duplicate creates, empty stubs, scattered `hasTable`/`hasColumn` guards
**34 models** in flat `app/Models/` directory
**6 service classes** - InventoryService (FIFO/LIFO), CartService, OrderService, ProductService, etc.
**Repository pattern** - dual implementation (old + Eloquent), partially used
**Blade-only views** - no Livewire components exist
**No Catalog entity** - previous `catalog_types` was created then stubbed out (empty migration)
**No multi-tenancy** - single database, single app
**No theme system** - hardcoded Blade templates

### Current Table Map (30 tables)

| Domain | Tables |
|--------|--------|
| **Auth** | users, roles, permissions, role_permissions |
| **Catalog** | categories, brands, pricing_templates, category_pricing_template, category_attributes |
| **Product** | products, product_attributes, product_attribute_values, product_variants, variant_attributes |
| **Inventory** | purchase_batches, inventory_items, inventory_movements |
| **Order** | orders, order_items |
| **Customer** | addresses, wishlists, reviews |
| **Marketing** | coupons, flash_sales, flash_sale_products, loyalty_programs, loyalty_transactions |
| **Finance** | expenses, expense_categories, partners, partner_payments, partner_calculations, investments, investors, capital_accounts, financial_transactions |
| **Content** | cms_pages, settings, activity_logs |

### NON-NEGOTIABLE Systems (DO NOT TOUCH logic)

1. **InventoryService** - FIFO/LIFO batch deduction, stock reservation, weighted-average COGS
2. **PurchaseBatch** system - batch tracking, remaining_quantity, status lifecycle
3. **InventoryMovement** - audit trail for all stock changes
4. **InventoryItem** - multi-location warehouse tracking with virtual columns
5. **Financial transaction** system - capital accounts, partner calculations, investor tracking
6. **Order item COGS** - virtual `profit` column calculated from `unit_price - unit_cost`

---

## 1. DOMAIN RESTRUCTURE PROPOSAL

### Target Directory Structure

```
app/
├── Domains/
│   ├── Tenant/
│   │   ├── Models/
│   │   │   └── Tenant.php
│   │   │   └── Domain.php
│   │   │   └── TenantSetting.php
│   │   ├── Actions/
│   │   │   └── CreateTenant.php
│   │   │   └── SuspendTenant.php
│   │   └── Services/
│   │       └── TenantService.php
│   │
│   ├── Site/
│   │   ├── Models/
│   │   │   └── Site.php
│   │   │   └── SiteThemeSetting.php
│   │   │   └── SiteSetting.php
│   │   ├── Actions/
│   │   │   └── CreateSite.php
│   │   │   └── SwitchSite.php
│   │   └── Services/
│   │       └── SiteService.php
│   │
│   ├── Catalog/
│   │   ├── Models/
│   │   │   └── Catalog.php
│   │   │   └── Category.php          ← moved from Models/
│   │   │   └── Brand.php             ← moved from Models/
│   │   │   └── PricingTemplate.php   ← moved from Models/
│   │   ├── Actions/
│   │   │   └── CreateCatalog.php
│   │   │   └── SwitchCatalog.php
│   │   │   └── SyncCatalogPricing.php
│   │   └── Services/
│   │       └── CatalogService.php
│   │       └── CategoryService.php   ← moved from Services/
│   │
│   ├── Product/
│   │   ├── Models/
│   │   │   └── Product.php           ← moved from Models/
│   │   │   └── ProductVariant.php    ← moved from Models/
│   │   │   └── ProductAttribute.php  ← moved from Models/
│   │   │   └── ProductAttributeValue.php
│   │   │   └── VariantAttribute.php
│   │   ├── Actions/
│   │   │   └── CreateProduct.php
│   │   │   └── UpdateProduct.php
│   │   │   └── DuplicateProduct.php
│   │   └── Services/
│   │       └── ProductService.php     ← moved from Services/Product/
│   │       └── ProductVariantService.php
│   │       └── VariantGeneratorService.php
│   │
│   ├── Inventory/
│   │   ├── Models/
│   │   │   └── PurchaseBatch.php      ← moved from Models/
│   │   │   └── InventoryItem.php      ← moved from Models/
│   │   │   └── InventoryMovement.php  ← moved from Models/
│   │   └── Services/
│   │       └── InventoryService.php   ← moved from Services/ (PRESERVE AS-IS)
│   │
│   ├── Order/
│   │   ├── Models/
│   │   │   └── Order.php              ← moved from Models/
│   │   │   └── OrderItem.php          ← moved from Models/
│   │   ├── Actions/
│   │   │   └── PlaceOrder.php
│   │   │   └── CancelOrder.php
│   │   │   └── UpdateOrderStatus.php
│   │   ├── Events/
│   │   │   └── OrderStatusChanged.php ← moved from Events/
│   │   ├── Listeners/
│   │   │   └── DeductInventory.php    ← moved from Listeners/
│   │   │   └── SendOrderConfirmation.php
│   │   │   └── UpdateOrderStatusHistory.php
│   │   └── Services/
│   │       └── OrderService.php       ← moved from Services/Order/
│   │       └── CartService.php        ← moved from Services/Cart/
│   │
│   ├── Customer/
│   │   ├── Models/
│   │   │   └── Address.php            ← moved from Models/
│   │   │   └── Wishlist.php           ← moved from Models/
│   │   │   └── Review.php             ← moved from Models/
│   │   └── Services/
│   │       └── CustomerService.php
│   │
│   ├── Marketing/
│   │   ├── Models/
│   │   │   └── Coupon.php             ← moved from Models/
│   │   │   └── FlashSale.php          ← moved from Models/
│   │   │   └── LoyaltyProgram.php     ← moved from Models/
│   │   │   └── LoyaltyTransaction.php
│   │   └── Services/
│   │       └── CouponService.php
│   │       └── LoyaltyService.php
│   │
│   ├── Accounting/
│   │   ├── Models/
│   │   │   └── Expense.php              ← moved from Models/
│   │   │   └── ExpenseCategory.php      ← moved from Models/
│   │   │   └── Partner.php              ← moved from Models/
│   │   │   └── PartnerPayment.php       ← moved from Models/
│   │   │   └── PartnerCalculation.php   ← moved from Models/
│   │   │   └── Investment.php           ← moved from Models/
│   │   │   └── Investor.php             ← moved from Models/
│   │   │   └── CapitalAccount.php       ← moved from Models/
│   │   │   └── FinancialTransaction.php ← moved from Models/
│   │   └── Services/
│   │       └── FinancialCalculationService.php ← moved from Services/
│   │       └── PartnerInvestorService.php
│   │
│   ├── Content/
│   │   ├── Models/
│   │   │   └── CmsPage.php           ← moved from Models/
│   │   │   └── Setting.php           ← moved from Models/
│   │   └── Services/
│   │       └── PageService.php
│   │
│   └── Auth/
│       ├── Models/
│       │   └── Role.php               ← moved from Models/
│       │   └── Permission.php         ← moved from Models/
│       └── Services/
│           └── RoleService.php
│
├── Livewire/
│   ├── Admin/
│   │   ├── Layouts/
│   │   │   └── AdminLayout.php
│   │   │   └── Sidebar.php
│   │   │   └── Header.php
│   │   ├── Dashboard/
│   │   │   └── DashboardPage.php
│   │   │   └── StatsWidget.php
│   │   ├── Catalog/
│   │   │   └── CatalogManager.php
│   │   │   └── CategoryManager.php
│   │   │   └── BrandManager.php
│   │   ├── Product/
│   │   │   └── ProductList.php
│   │   │   └── ProductForm.php
│   │   │   └── VariantManager.php
│   │   ├── Inventory/
│   │   │   └── InventoryList.php
│   │   │   └── PurchaseBatchForm.php
│   │   │   └── MovementLog.php
│   │   ├── Order/
│   │   │   └── OrderList.php
│   │   │   └── OrderDetail.php
│   │   ├── Customer/
│   │   │   └── CustomerList.php
│   │   ├── Marketing/
│   │   │   └── CouponManager.php
│   │   │   └── FlashSaleManager.php
│   │   ├── Accounting/
│   │   │   └── ExpenseManager.php
│   │   │   └── PartnerManager.php
│   │   │   └── InvestmentManager.php
│   │   ├── Reports/
│   │   │   └── ReportDashboard.php
│   │   ├── Settings/
│   │   │   └── SettingsManager.php
│   │   │   └── RoleManager.php
│   │   └── Theme/
│   │       └── ThemeBuilder.php
│   │       └── SectionEditor.php
│   │
│   └── Storefront/
│       ├── Pages/
│       │   └── HomePage.php
│       │   └── CatalogPage.php
│       │   └── ProductPage.php
│       │   └── CartPage.php
│       │   └── CheckoutPage.php
│       │   └── AccountPage.php
│       ├── Components/
│       │   └── Navigation.php
│       │   └── Footer.php
│       │   └── CartDrawer.php
│       │   └── SearchBar.php
│       │   └── CatalogSwitcher.php
│       └── Sections/                 ← Theme-controlled sections
│           └── HeroSection.php
│           └── FeaturedProducts.php
│           └── CategoryGrid.php
│           └── BannerSection.php
│           └── ProductCarousel.php
│           └── NewsletterSection.php
│           └── TestimonialSection.php
│
├── Themes/
│   ├── ThemeRegistry.php             ← Central theme registration
│   ├── ThemeRenderer.php             ← Resolves sections from JSON config
│   ├── Contracts/
│   │   └── ThemeInterface.php
│   ├── DefaultTheme/
│   │   ├── theme.json                ← Theme metadata + defaults
│   │   ├── layouts/
│   │   │   └── master.blade.php
│   │   ├── sections/
│   │   │   └── hero.blade.php
│   │   │   └── featured-products.blade.php
│   │   │   └── category-grid.blade.php
│   │   │   └── banner.blade.php
│   │   │   └── newsletter.blade.php
│   │   ├── components/
│   │   │   └── product-card.blade.php
│   │   │   └── category-card.blade.php
│   │   │   └── price-display.blade.php
│   │   └── tokens.css                ← CSS variables for this theme
│   └── ModernTheme/
│       ├── theme.json
│       ├── layouts/
│       ├── sections/
│       ├── components/
│       └── tokens.css
│
├── Http/
│   ├── Controllers/
│   │   └── Api/V1/                   ← Future API controllers
│   ├── Middleware/
│   │   └── EnsureAdmin.php           ← kept
│   │   └── IdentifyTenant.php        ← new
│   │   └── ResolveSite.php           ← new
│   │   └── ResolveCatalog.php        ← new
│   └── Requests/                     ← kept, reorganized later
│
├── Providers/
│   └── AppServiceProvider.php
│   └── EventServiceProvider.php
│   └── DomainServiceProvider.php     ← new: registers domain bindings
│   └── ThemeServiceProvider.php      ← new: registers theme engine
│
└── Support/
    ├── Enums/
    │   └── OrderStatus.php
    │   └── MovementType.php
    │   └── CatalogType.php
    │   └── BatchStatus.php
    ├── Traits/
    │   └── HasSlug.php
    │   └── BelongsToCatalog.php
    │   └── BelongsToSite.php
    └── Cache/
        └── CacheKeys.php
```

### Key Relationships

```
Tenant (multi-tenant root)
  └── Site (a tenant can have multiple sites/storefronts)
       └── Catalog (B2B / B2C / Regional)
            ├── Category (scoped per catalog)
            │    └── Product
            │         ├── ProductVariant
            │         │    └── InventoryItem ← links to PurchaseBatch
            │         ├── PurchaseBatch (FIFO/LIFO preserved)
            │         └── InventoryMovement (audit trail preserved)
            └── PricingTemplate (catalog-level pricing rules)

Tenant
  └── Users (admin, staff)
  └── Orders ← created within a Site context
  └── Customers
  └── Accounting (expenses, partners, investments)
```

### Migration Strategy for Model Moves

**No namespace changes during initial restructure.** We use the following approach:

1. Move files to new locations
2. Update namespace declarations
3. Add class aliases in `DomainServiceProvider` for backward compatibility during transition
4. Update imports across all files

This avoids breaking the running application during restructure.

---

## 2. MIGRATION REDESIGN OUTLINE

### Problem

Current state: **65+ migration files** with:
- Patch-on-patch (`add_x_to_y`, `fix_x_columns`, `backfill_x`)
- Duplicate create attempts with `if (!Schema::hasTable(...))` guards
- Empty stub migrations (catalog_types voided out)
- `consolidate_tables.php` that only creates `settings`
- Data seeding mixed into migrations (`expense_categories`, `order_number` backfill)

### Strategy: Clean Slate Rewrite

We will create a **single consolidated migration set** under `database/migrations/tenant/` with clear ordering.

**Existing 65+ files** will be moved to `database/migrations/_archive/` (not deleted, for reference).

### New Migration Order

All migrations use the format `YYYY_MM_DD_NNNNNN` with sequential numbering:

```
database/
├── migrations/
│   ├── _archive/                     ← OLD migrations preserved
│   │   └── [all 65+ existing files]
│   │
│   ├── tenant/                       ← CLEAN tenant migrations
│   │   ├── 0001_create_users_table.php
│   │   ├── 0002_create_roles_and_permissions_tables.php
│   │   ├── 0003_create_settings_table.php
│   │   ├── 0004_create_catalogs_table.php              ← NEW
│   │   ├── 0005_create_categories_table.php             ← catalog_id FK
│   │   ├── 0006_create_brands_table.php
│   │   ├── 0007_create_product_attributes_table.php
│   │   ├── 0008_create_products_table.php               ← catalog_id FK
│   │   ├── 0009_create_product_variants_table.php
│   │   ├── 0010_create_variant_attributes_table.php
│   │   ├── 0011_create_purchase_batches_table.php       ← PRESERVED
│   │   ├── 0012_create_inventory_items_table.php        ← PRESERVED
│   │   ├── 0013_create_inventory_movements_table.php    ← PRESERVED
│   │   ├── 0014_create_orders_table.php                 ← consolidated
│   │   ├── 0015_create_order_items_table.php            ← consolidated
│   │   ├── 0016_create_addresses_table.php
│   │   ├── 0017_create_wishlists_table.php
│   │   ├── 0018_create_reviews_table.php
│   │   ├── 0019_create_coupons_table.php
│   │   ├── 0020_create_flash_sales_table.php
│   │   ├── 0021_create_loyalty_tables.php
│   │   ├── 0022_create_cms_pages_table.php
│   │   ├── 0023_create_activity_logs_table.php
│   │   ├── 0024_create_expense_categories_table.php
│   │   ├── 0025_create_expenses_table.php
│   │   ├── 0026_create_partners_table.php
│   │   ├── 0027_create_partner_payments_table.php
│   │   ├── 0028_create_partner_calculations_table.php
│   │   ├── 0029_create_investors_table.php
│   │   ├── 0030_create_investments_table.php
│   │   ├── 0031_create_capital_accounts_table.php
│   │   ├── 0032_create_financial_transactions_table.php
│   │   ├── 0033_create_pricing_templates_table.php
│   │   ├── 0034_create_pivot_tables.php                 ← category_attributes, category_pricing_template
│   │   ├── 0035_create_sites_table.php                  ← NEW
│   │   └── 0036_create_site_theme_settings_table.php    ← NEW
│   │
│   └── central/                      ← FUTURE: multi-tenant central DB
│       ├── 0001_create_tenants_table.php
│       ├── 0002_create_domains_table.php
│       └── 0003_create_subscriptions_table.php
│
└── seeders/
    ├── DatabaseSeeder.php            ← Rewritten as orchestrator
    ├── Tenant/
    │   ├── UserSeeder.php
    │   ├── RolePermissionSeeder.php
    │   ├── SettingSeeder.php
    │   ├── CatalogSeeder.php         ← NEW
    │   ├── CategorySeeder.php        ← scoped to catalogs
    │   ├── BrandSeeder.php
    │   ├── AttributeSeeder.php
    │   ├── ProductSeeder.php         ← scoped to catalogs
    │   ├── PricingTemplateSeeder.php
    │   └── SampleOrderSeeder.php
    └── _archive/
        └── [old seeders]
```

### Key Schema Changes in Clean Rewrite

#### NEW: `catalogs` table (0004)

```
catalogs
├── id (bigint, PK)
├── site_id (FK → sites, nullable for now)
├── name (string)
├── slug (string, unique per site)
├── description (text, nullable)
├── type (enum: b2c, b2b, regional, wholesale)
├── target_audience (string, nullable)
├── region_codes (json, nullable)
├── currency (string, default BDT)
├── language (string, default en)
├── is_active (boolean, default true)
├── is_default (boolean, default false)
├── sort_order (integer, default 0)
├── settings (json, nullable)
├── pricing_rules (json, nullable)
├── timestamps
├── soft_deletes
├── INDEX (site_id, is_active)
├── INDEX (type)
```

#### CHANGED: `categories` table (0005)

```diff
categories
+ ├── catalog_id (FK → catalogs, nullable, cascadeOnDelete)
  ├── name (string)                  ← removed unique constraint (unique per catalog)
  ├── slug (string)                  ← removed unique constraint (unique per catalog)
  ├── description (text, nullable)
  ├── icon (string, nullable)
+ ├── image (string, nullable)       ← NEW
  ├── parent_id (FK → categories, nullable)
  ├── show_on_home (boolean)
  ├── is_active (boolean)
  ├── sort_order (integer)
  ├── timestamps
+ ├── soft_deletes                   ← NEW
+ ├── UNIQUE (catalog_id, slug)      ← CHANGED: scoped unique
+ ├── INDEX (catalog_id, is_active)  ← NEW
```

#### CHANGED: `products` table (0008)

```diff
products
+ ├── catalog_id (FK → catalogs, nullable, nullOnDelete)
  ├── name (string)
  ├── slug (string)                  ← unique per catalog, not globally
  ├── sku (string, nullable)         ← unique per catalog, not globally
  ├── price (decimal 10,2)
  ├── cost_price (decimal 10,2, nullable)
  ├── discount_price (decimal 10,2, nullable)
  ├── discount_type (string, default percentage)
  ├── vat_rate (decimal 5,2, default 0)
  ├── wholesale_price (decimal 10,2, nullable)
+ ├── profit_margin (decimal 5,2, nullable)
  ├── short_description (text, nullable)
  ├── description (longtext, nullable)
  ├── primary_image (string, nullable)
  ├── images (json, nullable)
  ├── video_url (string, nullable)
  ├── product_type (string, default simple)
  ├── delivery_type (string, default standard)
  ├── barcode (string, nullable)
  ├── category_id (FK → categories, nullable)
  ├── brand_id (FK → brands, nullable)
  ├── stock_quantity (integer, default 0)
  ├── low_stock_alert (integer, default 5)
  ├── stock_status (string, default in_stock)
  ├── is_featured (boolean, default false)
- ├── status (enum active/inactive)
+ ├── is_active (boolean, default true)   ← CHANGED: boolean instead of enum
  ├── weight (decimal 8,2, nullable)
  ├── length (decimal 8,2, nullable)
  ├── width (decimal 8,2, nullable)
  ├── height (decimal 8,2, nullable)
  ├── tags (json, nullable)
  ├── custom_attributes (json, nullable)
  ├── meta_title (string, nullable)
  ├── meta_description (string 500, nullable)
  ├── features (text, nullable)
  ├── care_instructions (text, nullable)
  ├── ingredients (text, nullable)
  ├── safety_warning (text, nullable)
  ├── halal_certified (boolean, default false)
  ├── organic_certified (boolean, default false)
  ├── return_policy (text, nullable)
  ├── warranty (string, nullable)
  ├── manufacturer (string, nullable)
+ ├── sold_count (integer, default 0)    ← consolidated
  ├── timestamps
+ ├── soft_deletes                        ← NEW
+ ├── UNIQUE (catalog_id, slug)
+ ├── UNIQUE (catalog_id, sku)
+ ├── INDEX (catalog_id, is_active, is_featured)
```

#### CONSOLIDATED: `orders` table (0014)

All scattered add-column migrations merged into one clean create:

```
orders
├── id (bigint, PK)
├── order_number (string 30, unique)
├── user_id (FK → users, nullable)
├── customer_name (string)
├── customer_email (string, nullable)
├── customer_phone (string)
├── address_line (string)
├── city (string)
├── postal_code (string, nullable)
├── notes (text, nullable)
├── subtotal (decimal 10,2, default 0)
├── tax_amount (decimal 10,2, default 0)
├── total_amount (decimal 10,2)          ← CHANGED: decimal instead of unsignedInteger
├── payment_method (string, default cod)
├── status (string, default pending)
├── status_notes (text, nullable)
├── cancellation_reason (text, nullable)
├── timestamps
├── soft_deletes                          ← NEW
├── INDEX (status)
├── INDEX (user_id)
├── INDEX (created_at)
```

#### CONSOLIDATED: `order_items` table (0015)

```
order_items
├── id (bigint, PK)
├── order_id (FK → orders, cascade)
├── product_id (FK → products, cascade)
├── product_variant_id (FK → product_variants, nullable, set null)
├── quantity (integer)
├── unit_price (decimal 10,2)            ← CHANGED: decimal instead of unsignedInteger
├── unit_cost (decimal 10,2, nullable)   ← consolidated from patch migration
├── total_price (decimal 10,2)           ← CHANGED: decimal instead of unsignedInteger
├── purchase_batch_id (FK, nullable)     ← consolidated
├── profit (decimal 10,2, virtual)       ← PRESERVED: virtualAs formula
├── timestamps
```

#### INVENTORY TABLES: Preserved exactly

`purchase_batches`, `inventory_items`, `inventory_movements` - schemas preserved exactly as-is. Only change: the `inventory_movements` variant column from patch migration is included in the base create.

#### NEW: `sites` table (0035)

```
sites
├── id (bigint, PK)
├── name (string)
├── slug (string, unique)
├── domain (string, nullable, unique)
├── is_active (boolean, default true)
├── is_default (boolean, default false)
├── locale (string, default en)
├── currency (string, default BDT)
├── timezone (string, default Asia/Dhaka)
├── settings (json, nullable)
├── timestamps
├── soft_deletes
```

#### NEW: `site_theme_settings` table (0036)

```
site_theme_settings
├── id (bigint, PK)
├── site_id (FK → sites, cascade)
├── active_theme (string, default 'default')
├── colors (json, nullable)                     ← primary, secondary, accent, etc.
├── typography (json, nullable)                  ← font_family, heading_font, sizes
├── layout_config (json, nullable)              ← homepage section order + visibility
├── custom_css (text, nullable)                 ← tenant CSS overrides
├── header_config (json, nullable)              ← logo, nav style, announcement bar
├── footer_config (json, nullable)              ← columns, links, social
├── timestamps
├── UNIQUE (site_id)
```

### Inventory Schema Integrity Check

The following columns and constraints are **preserved exactly**:

- `purchase_batches.remaining_quantity` - decremented by InventoryService
- `purchase_batches.quantity_reserved` - reservation system
- `purchase_batches.status` - lifecycle (active → partially_sold → sold → expired → damaged)
- `inventory_items.quantity_available` - **virtual column**: `quantity_on_hand - quantity_reserved`
- `inventory_items.total_value` - **virtual column**: `quantity_on_hand * unit_cost`
- `inventory_movements.movement_type` - FIFO/LIFO audit trail
- `order_items.profit` - **virtual column**: `(unit_price - COALESCE(unit_cost, 0)) * quantity`

---

## 3. THEME ENGINE ARCHITECTURE

### Core Concept

The theme engine follows a **WordPress-like approach** adapted for Livewire:

1. **Themes** define visual structure (layouts, section templates, CSS tokens)
2. **Sections** are reusable Livewire components (HeroSection, FeaturedProducts, etc.)
3. **Layout config** (JSON) defines which sections appear, in what order, with what data
4. **Tenants/Sites** can customize colors, typography, section order via admin panel
5. **Theme Builder** admin page provides live preview of changes

### Architecture Diagram

```
┌─────────────────────────────────────────────────────────┐
│                    THEME ENGINE                          │
│                                                         │
│  ThemeRegistry (discovers + registers available themes)  │
│  ThemeRenderer (resolves theme → layout → sections)      │
│  ThemeServiceProvider (boots the engine)                 │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│                    THEME FILES                           │
│                                                         │
│  themes/default/                                        │
│  ├── theme.json       ← metadata, default config        │
│  ├── tokens.css       ← CSS variables                   │
│  ├── layouts/master.blade.php                           │
│  ├── sections/        ← blade partials per section      │
│  └── components/      ← blade partials for cards, etc.  │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│                 SITE THEME SETTINGS                      │
│           (stored in site_theme_settings)                │
│                                                         │
│  active_theme: "default"                                │
│  colors: { primary: "#4F46E5", ... }                    │
│  typography: { font: "Inter", ... }                     │
│  layout_config: {                                       │
│    "homepage": [                                        │
│      { "type": "hero", "enabled": true, "config": {} },│
│      { "type": "featured-products", "enabled": true },  │
│      { "type": "category-grid", "enabled": true },      │
│      { "type": "banner", "enabled": false },            │
│      { "type": "newsletter", "enabled": true }          │
│    ]                                                    │
│  }                                                      │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│              LIVEWIRE SECTION COMPONENTS                 │
│                                                         │
│  Each section is a Livewire component that:             │
│  - Receives its config from layout_config JSON          │
│  - Fetches its own data (e.g. featured products query)  │
│  - Renders using the active theme's section template    │
│  - Is independently lazy-loadable                       │
└─────────────────────────────────────────────────────────┘
```

### theme.json Schema

```json
{
    "name": "Default Theme",
    "slug": "default",
    "version": "1.0.0",
    "author": "KiddosHeaven",
    "description": "Clean, modern storefront theme",
    "preview": "themes/default/preview.png",
    "supports": ["hero", "featured-products", "category-grid", "banner", "product-carousel", "newsletter", "testimonials"],
    "defaults": {
        "colors": {
            "primary": "#4F46E5",
            "primary-foreground": "#FFFFFF",
            "secondary": "#F59E0B",
            "background": "#FFFFFF",
            "foreground": "#111827",
            "muted": "#F3F4F6",
            "border": "#E5E7EB",
            "destructive": "#EF4444",
            "success": "#10B981"
        },
        "typography": {
            "font_family": "Inter, sans-serif",
            "heading_font": "Inter, sans-serif",
            "base_size": "16px",
            "heading_weight": "700"
        },
        "layout": {
            "homepage": [
                {"type": "hero", "enabled": true, "config": {"style": "full-width"}},
                {"type": "featured-products", "enabled": true, "config": {"count": 8, "columns": 4}},
                {"type": "category-grid", "enabled": true, "config": {"count": 6, "columns": 3}},
                {"type": "banner", "enabled": true, "config": {"style": "split"}},
                {"type": "product-carousel", "enabled": true, "config": {"title": "New Arrivals", "query": "newest", "count": 12}},
                {"type": "newsletter", "enabled": true, "config": {}}
            ]
        }
    }
}
```

### Section Rendering Flow

```
1. User visits homepage
2. ThemeRenderer loads active theme + site_theme_settings
3. Reads layout_config.homepage array
4. For each enabled section:
   a. Resolves Livewire component: Storefront\Sections\{PascalCase(type)}
   b. Passes section config as props
   c. Component fetches its own data
   d. Component renders via: themes/{active_theme}/sections/{type}.blade.php
5. CSS variables injected from colors/typography config
```

### Homepage Blade Template (simplified)

```blade
{{-- resources/views/themes/default/layouts/master.blade.php --}}
<html>
<head>
    <style>
        :root {
            --color-primary: {{ $themeColors['primary'] ?? '#4F46E5' }};
            --color-secondary: {{ $themeColors['secondary'] ?? '#F59E0B' }};
            --font-family: {{ $typography['font_family'] ?? 'Inter, sans-serif' }};
            /* ... all tokens ... */
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    @livewire('storefront.components.navigation')

    <main>
        {{ $slot }}
    </main>

    @livewire('storefront.components.footer')
    @livewireScripts
</body>
</html>

{{-- resources/views/livewire/storefront/pages/home-page.blade.php --}}
@foreach($sections as $section)
    @if($section['enabled'])
        @livewire('storefront.sections.' . $section['type'], ['config' => $section['config'] ?? []], key($section['type']))
    @endif
@endforeach
```

### Theme Builder Panel (Admin)

The admin Theme Builder allows:

1. **Select active theme** - dropdown of registered themes
2. **Customize colors** - color pickers for all design tokens
3. **Customize typography** - font selector, size adjustments
4. **Rearrange homepage sections** - drag/drop reordering
5. **Enable/disable sections** - toggle switches
6. **Configure sections** - per-section settings (count, columns, style)
7. **Live preview** - iframe showing changes in real-time
8. **Save** - writes to `site_theme_settings` table

This is a Livewire admin component: `Livewire\Admin\Theme\ThemeBuilder`

---

## 4. IMPLEMENTATION SEQUENCE

### Phase 1A: Domain Restructure (files only, no logic changes)

1. Create directory structure under `app/Domains/`
2. Move models to domain folders, update namespaces
3. Move services to domain folders, update namespaces
4. Move events/listeners to domain folders
5. Create `DomainServiceProvider` with class aliases for backward compat
6. Update all import statements
7. Verify all routes still work

### Phase 1B: Migration Rewrite

1. Archive existing 65+ migrations to `_archive/`
2. Write clean consolidated migrations under `tenant/`
3. Add `catalogs` table with B2B/B2C/Regional support
4. Add `catalog_id` FK to `categories` and `products`
5. Add `sites` and `site_theme_settings` tables
6. Change `orders.total_amount` from unsignedInteger to decimal
7. Consolidate all order/product column patches into base creates
8. Preserve inventory schemas exactly (purchase_batches, inventory_items, inventory_movements)
9. Fresh migrate + test

### Phase 1C: Seeder Rewrite

1. Archive existing seeders
2. Create modular seeders under `seeders/Tenant/`
3. CatalogSeeder: creates B2C (default), B2B, Regional catalogs
4. CategorySeeder: creates categories per catalog
5. ProductSeeder: distributes products across catalogs
6. All other seeders cleaned up and modularized

### Phase 1D: Theme Engine Core

1. Create `ThemeRegistry`, `ThemeRenderer`, `ThemeServiceProvider`
2. Create `ThemeInterface` contract
3. Create `DefaultTheme` with theme.json, tokens.css, section templates
4. Create `site_theme_settings` model and migration
5. Wire up section rendering in homepage

---

## 5. FILES TO CREATE / MODIFY

### New Files (Phase 1)
- `app/Domains/` - entire domain structure (directories + moved files)
- `app/Providers/DomainServiceProvider.php`
- `app/Providers/ThemeServiceProvider.php`
- `app/Themes/ThemeRegistry.php`
- `app/Themes/ThemeRenderer.php`
- `app/Themes/Contracts/ThemeInterface.php`
- `app/Themes/DefaultTheme/theme.json`
- `app/Themes/DefaultTheme/tokens.css`
- `app/Support/Enums/*.php`
- `database/migrations/tenant/` - all 36 clean migrations
- `database/seeders/Tenant/` - all modular seeders

### Modified Files
- `app/Providers/AppServiceProvider.php` - register new providers
- `composer.json` - PSR-4 autoload for Domains namespace
- `config/app.php` - register providers

### Archived (not deleted)
- `database/migrations/` - all 65+ existing files → `_archive/`
- `database/seeders/` - old seeders → `_archive/`

---

## AWAITING APPROVAL

Before generating any implementation code, please confirm:

1. **Domain structure** - Is the folder structure acceptable? Any domains to add/rename?
2. **Site entity** - Do you want `Site` as a concept (Tenant → Site → Catalog) or is Catalog directly under Tenant?
3. **Migration approach** - Archive existing + fresh rewrite acceptable? (existing DB will need `migrate:fresh`)
4. **Theme engine** - JSON-config-driven section approach acceptable?
5. **Inventory preservation** - Confirmed: no changes to FIFO/LIFO/batch logic?
6. **Enums vs strings** - Should we use PHP 8.1 Enums for OrderStatus, MovementType, etc.?

**Reply with approval and I will begin Phase 1A (domain restructure).**
