# Kiddo's Heaven - Project Structure Documentation

## Tech Stack

- **Framework:** Laravel 12
- **PHP:** 8.2+
- **Database:** MySQL
- **Frontend:** Tailwind CSS 4.0, Vite
- **Authentication:** Laravel Auth with custom admin middleware

## Directory Structure

### Models (`app/Models/`)

- **User** - Users (customers + admins via `is_admin` flag)
- **Product** - Products with variants, attributes, pricing (BDT), cost tracking
- **Catalog** - Product categories with types and attributes
- **CatalogType** - Reusable catalog type templates
- **Brand** - Product brands
- **Order** - Customer orders
- **OrderItem** - Order line items
- **ProductAttribute** - Filterable product attributes (Color, Size, etc.)
- **ProductAttributeValue** - Attribute value options
- **Review** - Product reviews with ratings
- **Wishlist** - Customer wishlists
- **Coupon** - Discount codes
- **FlashSale** - Time-limited sales
- **Address** - Customer shipping/billing addresses
- **PurchaseBatch** - Inventory purchase batches
- **InventoryMovement** - Stock movement tracking
- **Expense** - Business expenses
- **ExpenseCategory** - Expense categorization
- **Partner** - Business partners
- **PartnerCalculation** - Partner commission calculations
- **PartnerPayment** - Partner payment records
- **Investment** - Business investments
- **Investor** - Investment partners
- **CapitalAccount** - Capital account management
- **FinancialTransaction** - Financial transaction records
- **CmsPage** - Dynamic CMS pages
- **Setting** - System settings (key-value)
- **Permission** - User permissions
- **Role** - User roles
- **LoyaltyProgram** - Customer loyalty programs
- **LoyaltyTransaction** - Loyalty points transactions

### Controllers

#### Admin Controllers (`app/Http/Controllers/Admin/`)

- **DashboardController** - Admin dashboard with stats
- **CatalogController** - Catalog CRUD + reordering + attribute management
- **CatalogTypeController** - Catalog type templates + attribute templates
- **ProductController** - Product CRUD + bulk actions
- **ProductAttributeController** - Attribute CRUD + value management + catalog/type associations
- **BrandController** - Brand CRUD + toggle status
- **OrderController** - Order management + status updates + invoice + export + shipping
- **InventoryController** - Stock management + alerts
- **PurchaseBatchController** - Purchase batch tracking
- **InventoryMovementController** - Stock movement logs
- **CustomerController** - Customer management + toggle status
- **CouponController** - Coupon CRUD
- **FlashSaleController** - Flash sale CRUD + toggle status
- **CmsPageController** - CMS page CRUD
- **ReviewController** - Review moderation + approve/reject + bulk actions
- **CapitalAccountController** - Capital account CRUD
- **FinancialTransactionController** - Transaction CRUD
- **ExpenseController** - Expense CRUD + approval + categories
- **PartnerController** - Partner CRUD + payments + commission calculations
- **PartnerCalculationController** - Calculation approval + mark paid
- **InvestmentController** - Investment CRUD + status updates
- **ReportController** - Sales/inventory/batch reports
- **SettingController** - System settings
- **PermissionController** - Permission management
- **RoleController** - Role management
- **LoyaltyController** - Loyalty program management

#### Frontend Controllers (`app/Http/Controllers/`)

- **ShopController** - Home, catalog, product detail
- **CartController** - Cart CRUD
- **CheckoutController** - Checkout + order placement + thank you
- **Shop/SearchController** - Product search
- **Shop/OrderTrackingController** - Track order status
- **Customer/AccountController** - Customer profile
- **Customer/OrderController** - Order history
- **Auth/LoginController** - Login (customer + admin)
- **Auth/RegisterController** - Customer registration
- **PageController** - Dynamic page display

### Routes

#### Admin Routes (`routes/admin.php`)

- Prefix: `/admin`
- Middleware: `['auth', 'admin']`
- Organized by feature area:
    - Dashboard
    - Catalogs (with nested types and attributes)
    - Attributes (global)
    - Products (with bulk actions)
    - Brands
    - Orders (with status updates, invoice, shipping)
    - Inventory (stock management, purchase batches, movements)
    - Customers
    - Marketing (coupons, flash sales)
    - CMS Pages
    - Reviews
    - Finance (capital accounts, transactions, expenses, partners, investments)
    - Reports
    - Loyalty Programs
    - Settings
    - Permissions & Roles

#### Public Routes (`routes/web.php`)

- Home, catalog, product detail
- Cart, checkout
- Search
- Order tracking
- Customer authentication
- Customer account/orders
- Static pages

### Database Schema Key Points

#### Products Table

- Pricing in BDT (not cents)
- `cost_price` + `profit_margin` tracking
- `product_type`: simple, variable, digital
- `delivery_type`: instant, schedule, frozen
- `stock_quantity` + `low_stock_alert`
- `custom_attributes` (JSON) for flexible options
- `variants` (JSON) for product variations
- `images` (JSON array) with alt text support
- `tags` (JSON array)
- Relationships: Catalog, Brand, Reviews, Wishlist

#### Orders Table

- `status`: pending, processing, shipped, delivered, cancelled
- `payment_method`: cod (future: bkash, nagad, etc.)
- Relationships: User, OrderItems, Address

#### Catalogs Table

- `type` (FK to CatalogType slug)
- `show_on_home` flag
- Many-to-many with ProductAttribute via `catalog_attributes` pivot

#### Users Table

- `is_admin` flag for admin access
- Relationships: Orders, Reviews, Wishlist, Addresses

### Views Structure

#### Admin Views (`resources/views/admin/`)

- `layout.blade.php` - Fixed top header with nav
- `dashboard.blade.php` - Stats and charts
- Organized by feature in subdirectories
- Components in `layouts/` and feature-specific folders

#### Shop Views (`resources/views/shop/`)

- `home.blade.php` - Homepage
- `catalog.blade.php` - Product listing
- `product.blade.php` - Product detail
- `cart.blade.php` - Shopping cart
- `checkout.blade.php` - Checkout form
- `thankyou.blade.php` - Order confirmation

#### Components (`resources/views/components/`)

- Reusable Blade components for UI elements

### Key Features Implemented

#### Product Management

- ✅ Full CRUD with image upload (drag-and-drop)
- ✅ Image alt text support
- ✅ Product preview modal before saving
- ✅ Custom attributes (color, size, etc.)
- ✅ Variants support
- ✅ SKU, barcode tracking
- ✅ Cost price + profit margin calculation
- ✅ Wholesale pricing
- ✅ Weight/dimensions
- ✅ Video URL
- ✅ SEO meta fields
- ✅ Status (active/inactive)
- ✅ Featured flag
- ✅ Stock quantity + low stock alerts

#### Catalog System

- ✅ Hierarchical catalogs with types
- ✅ Reorderable catalog display
- ✅ Catalog-specific attributes
- ✅ Attribute templates via CatalogType

#### Order Management

- ✅ Order listing with pagination
- ✅ Order status workflow
- ✅ Invoice generation
- ✅ Bulk status updates
- ✅ Export functionality
- ✅ Shipping tracking

#### Inventory System

- ✅ Stock tracking
- ✅ Purchase batch management
- ✅ Inventory movement logs
- ✅ Low stock alerts

#### Marketing Features

- ✅ Coupon system
- ✅ Flash sales with time limits
- ✅ Product reviews with moderation
- ✅ Loyalty programs

#### Financial Management

- ✅ Capital account tracking
- ✅ Financial transactions
- ✅ Expense management with approval workflow
- ✅ Partner commission calculations
- ✅ Investment tracking

### Custom CSS Variables

#### Colors

- `--color-primary-dark`: #005461
- `--color-primary`: #018790
- `--color-primary-light`: #02a5a4
- `--color-accent`: #00b7b5
- `--color-success`: #10b981
- `--color-warning`: #f59e0b
- `--color-danger`: #dc2626

#### Admin Specific

- `--admin-primary`: Same as primary
- `--admin-bg`: #f4f4f4

### Middleware

- **auth** - Laravel authentication
- **admin** - Custom middleware checking `is_admin` flag on User model

### Key Business Rules

1. **Pricing:** Always in BDT (not cents)
2. **Profit Margin:** Auto-calculated from cost_price and price
3. **Stock:** Decremented on order, tracked via inventory movements
4. **Product Variants:** Stored as JSON for flexibility
5. **Custom Attributes:** JSON field for product-specific options (e.g., color choices)
6. **Catalog Attributes:** Predefined filterable attributes attached to catalogs

### Current Enhancement Status

- ✅ Drag-and-drop image upload
- ✅ Image alt text support
- ✅ Product preview modal
- ⏳ Rich text editor for descriptions (pending)
- ⏳ Multi-language support (pending)

### Notes for Future Development

- Controllers contain business logic - consider Repository pattern
- No Form Request classes - validation inline in controllers
- Session-based cart - consider database persistence for logged-in users
- COD only payment - need bKash, Nagad, Rocket, Cards integration
- No email notifications yet - consider queue jobs for order confirmations
- No activity logging - consider audit trail for admin actions
