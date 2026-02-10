# Quick Reference - Kiddo's Heaven

## Essential Commands

```bash
# Development
php artisan serve
npm run dev

# Database
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# Routes
php artisan route:list
php artisan route:list | grep admin
php artisan route:list --path=admin/catalogs

# Cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Queue (if using)
php artisan queue:work
php artisan queue:listen
```

## Admin Access

- URL: `/admin/login`
- Users with `is_admin = 1` in database

## Key Models & Relationships

```
User (is_admin)
├── Orders
├── Reviews
├── Wishlist
└── Addresses

Product
├── Catalog
├── Brand
├── Reviews
└── Wishlist

Catalog
├── CatalogType (via type slug)
├── Products
└── ProductAttributes (pivot)

Order
├── User
├── OrderItems
└── Address

Partner
├── CapitalAccounts
├── PartnerCalculations
└── PartnerPayments
```

## Important File Locations

### Controllers

- Admin: `app/Http/Controllers/Admin/`
- Shop: `app/Http/Controllers/ShopController.php`
- Customer: `app/Http/Controllers/Customer/`

### Models

- `app/Models/`

### Views

- Admin: `resources/views/admin/`
- Shop: `resources/views/shop/`
- Components: `resources/views/components/`

### Routes

- Admin: `routes/admin.php`
- Public: `routes/web.php`

### Migrations

- `database/migrations/`

### Config

- Database: `config/database.php`
- App: `config/app.php`

## Pricing Notes

- All prices in BDT (Bangladeshi Taka)
- NOT in cents - direct taka amount
- `cost_price` = buying price
- `price` = selling price
- `profit_margin` = auto-calculated percentage

## Common Tasks

### Add New Product

1. Go to `/admin/products/create`
2. Fill form (name, catalog, price, cost_price, stock)
3. Upload images (drag-and-drop)
4. Add custom attributes if needed
5. Preview before saving

### Manage Orders

1. Go to `/admin/orders`
2. View order details
3. Update status: pending → processing → shipped → delivered
4. Print invoice if needed

### Low Stock Alert

1. Go to `/admin/inventory/alerts`
2. See products below threshold
3. Create purchase batch to restock

### Add Catalog Type

1. Go to `/admin/catalogs/types` (FIXED - now works!)
2. Create new type template
3. Attach product attributes
4. Use when creating catalogs

## Troubleshooting

### Route 404

- Check route order (static before wildcards)
- Run `php artisan route:clear`
- Verify in `php artisan route:list`

### Image Upload Issues

- Check `storage/app/public` is linked: `php artisan storage:link`
- Verify permissions: `chmod -R 775 storage`

### Database Errors

- Check `.env` database credentials
- Run `php artisan migrate` to ensure all tables exist
- Check foreign key constraints

### CSS Not Loading

- Run `npm run build` for production
- Run `npm run dev` for development
- Clear browser cache

## Documentation Files

- `docs/PROJECT_STRUCTURE.md` - Complete project overview
- `docs/ADMIN_ROUTES.md` - Route organization guide
- `docs/ROUTE_ORDER_FIX.md` - Recent route fix details
- `docs/QUICK_REFERENCE.md` - This file
