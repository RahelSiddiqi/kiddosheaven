# Catalog Connections Audit Report

**Date:** 2025-02-14  
**Scope:** Products, Categories, Attributes, Pricing Templates, Brands — models, migrations, controllers, and usage.

---

## 1. Summary

| Connection | Status | Notes |
|-----------|--------|------|
| **Product ↔ Category** | ✅ Connected | `Product belongsTo Category`; `Category hasMany Product`. Used in admin product CRUD and filters. |
| **Product ↔ Brand** | ✅ Connected | `Product belongsTo Brand`; `Brand hasMany Product`. Used in admin product CRUD and filters. |
| **Category ↔ Attributes** | ✅ Connected | Pivot `category_attributes`; Category has many ProductAttributes; used for variant attributes and product form. |
| **Attribute ↔ Attribute Values** | ✅ Connected | `ProductAttribute hasMany ProductAttributeValue`. Values used in variant generator and attribute edit. |
| **Product/Variant ↔ Attributes** | ✅ Connected | Variants use `variant_attributes` pivot (variant ↔ ProductAttribute + ProductAttributeValue). |
| **Category ↔ Pricing Templates** | ✅ Connected | Pivot `category_pricing_template`; Category belongsToMany PricingTemplate. |
| **Product ↔ Pricing Template** | ⚠️ Indirect only | Product has no direct link; template is reachable via `$product->category->pricingTemplates()`. Not auto-applied on save. |

---

## 2. Model Relationships (Verified)

### Product
- `category()` → BelongsTo Category ✅  
- `brand()` → BelongsTo Brand ✅  
- `variants()` → HasMany ProductVariant ✅  
- No direct `pricingTemplate` or `attributes()` — category holds both.

### Category
- `products()` → HasMany Product ✅  
- `attributes()` → BelongsToMany ProductAttribute (`category_attributes`) with pivot sort_order, is_required ✅  
- `pricingTemplates()` → BelongsToMany PricingTemplate (`category_pricing_template`) ✅  

### ProductAttribute
- `categories()` → BelongsToMany Category (`category_attributes`) ✅  
- `values()` → HasMany ProductAttributeValue ✅  

### ProductAttributeValue
- `attribute()` → BelongsTo ProductAttribute ✅  
- `product()` → BelongsTo Product (nullable; for product-specific values) ✅  
- **Missing:** `variantAttributes()` or `variants()` — used in old AttributeController for “used in variants” check; current AttributeValueController does not check usage before delete.

### ProductVariant
- `product()` → BelongsTo Product ✅  
- `variantAttributes()` → HasMany VariantAttribute ✅  
- `attributes()` → BelongsToMany ProductAttribute via `variant_attributes` with pivot product_attribute_value_id ✅  

### VariantAttribute
- `variant()` → BelongsTo ProductVariant ✅  
- `attribute()` → BelongsTo ProductAttribute ✅  
- `attributeValue()` → BelongsTo ProductAttributeValue ✅  

### Brand
- `products()` → HasMany Product ✅  

### PricingTemplate
- `categories()` → BelongsToMany Category (`category_pricing_template`) ✅  
- `calculatePrice(cost, attributes)` implemented ✅  

---

## 3. Database / Migrations

### Products table
- `category_id` (unsignedBigInteger, nullable) — **no foreign key** in migrations. Referential integrity not enforced.
- `brand_id` (unsignedBigInteger, nullable) — **no foreign key** in migrations. Referential integrity not enforced.

**Recommendation:** Add optional migrations to add `foreignId('category_id')->nullable()->constrained()->nullOnDelete()` and same for `brand_id` so orphaned or invalid IDs are prevented.

### Pivot tables
- `category_attributes`: category_id, product_attribute_id, is_required, sort_order — FKs present ✅  
- `category_pricing_template`: category_id, pricing_template_id — FKs present ✅  
- `variant_attributes`: product_variant_id, product_attribute_id, product_attribute_value_id — FKs present ✅  

### product_attribute_values
- Columns: id, product_id (nullable), product_attribute_id, value, price_modifier, sort_order.  
- **No columns:** `display_value`, `color_code`.  
- AttributeValueController store/update pass `display_value` and `color_code`; they are not in the model’s `$fillable` and not in the table, so they are ignored.

**Recommendation:** Either add `display_value` and `color_code` (migration + fillable) or remove them from the controller and any forms.

---

## 4. Bugs Fixed During Audit

### AttributeValueController::reorder
- **Issue:** Used `->where('attribute_id', $attribute->id)`. The table column is `product_attribute_id`.  
- **Fix:** Replaced with `->where('product_attribute_id', $attribute->id)`.

---

## 5. Gaps / Recommendations

1. **ProductAttributeValue and variants**
   - Add a relationship on `ProductAttributeValue` to variant usage, e.g. `variantAttributes()` → HasMany VariantAttribute.
   - Optionally in `AttributeValueController::destroy` check usage (e.g. `$attributeValue->variantAttributes()->exists()`) and block or warn before delete.

2. **Product / Brand foreign keys**
   - Add optional migrations to add FK constraints for `products.category_id` and `products.brand_id` (nullable, constrained, nullOnDelete) for data integrity.

3. **Attribute values: display_value & color_code**
   - Align controller and schema: add columns + fillable and use them, or remove from controller/UI.

4. **Pricing templates and product/variant price**
   - Templates are category-level and not auto-applied when saving product or variant price. If the intent is to “apply category template” on create/update, add a step in ProductService or variant flow that:
     - Resolves template via `PricingTemplateService::getTemplateForCategory($product->category)`.
     - Optionally calls `calculatePrice(cost, attributes)` and suggests or sets price (e.g. for new variants).

5. **Admin product flows**
   - Product create/edit already use category and brand; variant attributes are loaded by category (`/admin/products/variant-attributes/{categoryId}`). No change required for basic connectivity; only the above improvements if desired.

---

## 6. Frontend (Admin & Storefront) Audit

### 6.1 Storefront (customer-facing)

- **Routes (web.php):** Home, catalog, product show, search, track order, cart, checkout, account, customer orders. **No create/update/delete of products, categories, attributes, brands, or pricing templates** — storefront is read-only for catalog entities (and cart/orders for the logged-in customer).
- **How catalog is used:** `ShopController::home` uses categories and featured products; `catalog` filters products by category; `showProduct` shows one product and related by category. All read-only.

### 6.2 Admin: Create / Update / Delete (per entity)

| Entity | Create | Update | Delete | Where |
|--------|--------|--------|--------|-------|
| **Products** | ✅ Dedicated page `admin.products.create` | ✅ `admin.products.edit` | ✅ Per-row delete on index & show | products/index, show, edit |
| **Categories** | ✅ Modal on index (`admin.categories.store`) | ✅ `admin.categories.edit` | ✅ Per category (show/edit) | categories/index-new, show, edit |
| **Attributes** | ✅ Modal + create page | ✅ `admin.attributes.edit` | ✅ Per attribute | attributes/index, edit |
| **Attribute values** | ✅ Per value (form + “Add value”) | ✅ Inline / values edit page | ✅ Per value (delete button) | attributes/edit, values/edit, edit-values |
| **Pricing templates** | ✅ Modal on index | ✅ Modal edit | ✅ Per template | pricing-templates/index |
| **Brands** | ✅ Modal on index | ✅ Modal edit | ✅ Per brand | brands/index |

All of the above have proper routes and controller actions; create/update/delete are implemented for each entity (no “create all” or “delete all” at once — see bulk below).

### 6.3 Admin: Bulk / “Create all / Update all / Delete all” options

| Area | Bulk create | Bulk update | Bulk delete | Notes |
|------|-------------|-------------|-------------|--------|
| **Products (list)** | ❌ | ❌ | ❌ | Backend `bulkAction` exists (delete, activate, deactivate, feature, unfeature) but **no UI** on products index — no checkboxes, no “Select all”, no bulk dropdown. |
| **Product variants** | ✅ Generate all | ✅ Bulk update | ❌ | On product show: “Generate variants” creates all combinations from attributes; bulk bar updates price/cost/sale/stock for selected variants. No “delete all variants”. |
| **Categories** | ❌ | ❌ | ❌ | No bulk actions. |
| **Attributes** | ❌ | ❌ | ❌ | No bulk actions. |
| **Attribute values** | ⚠️ “Bulk import” UI only | ❌ | ❌ | “Bulk Import” modal sends **one** request with the whole textarea as a single `value`. Backend creates **one** value (max 255 chars). So pasting multiple lines creates one multiline value (or validation error) — **not** “create all” values from lines. |
| **Pricing templates** | ❌ | ❌ | ❌ | No bulk actions. |
| **Brands** | ❌ | ❌ | ❌ | No bulk actions. |
| **Orders** | ❌ | ✅ Bulk status | ❌ | Select orders → change status. |
| **Reviews** | ❌ | ✅ Bulk approve | ✅ Bulk delete | Select reviews → approve or delete. |

Summary:

- **Create all:** Only product **variants** have a real “create all” (generate combinations). Attribute values “bulk import” does **not** create multiple values from lines.
- **Update all:** Product **variants** (bulk price/cost/sale/stock), **orders** (bulk status), **reviews** (bulk approve). Products list has no bulk-update UI despite backend support.
- **Delete all:** **Reviews** have bulk delete. **Products** have a bulk delete action in the backend but **no UI** on the products index. No “delete all” for categories, attributes, brands, or pricing templates.

### 6.4 Recommendations (frontend)

1. **Products index:** Add checkboxes, “Select all”, and a bulk action dropdown (delete, activate, deactivate, feature, unfeature) that POSTs to `admin.products.bulk-action` so the existing backend is used.
2. **Attribute values “Bulk import”:** Either (a) change frontend to split textarea by line and call store once per line (loop), or (b) add a backend endpoint that accepts an array of values and creates multiple `ProductAttributeValue` rows; then call that from the modal. Today the UX suggests “create many” but only one value is created (and may fail validation if long).
3. **Optional:** “Delete all” for a given context (e.g. all attribute values for an attribute, or all variants for a product) could be added with confirmation; currently only per-item delete exists for these.

---

## 7. Conclusion

Products, categories, attributes, pricing templates, and brands are **correctly connected** at the model and pivot level. Product ↔ Category and Product ↔ Brand are used in admin. Category ↔ Attributes and Category ↔ Pricing Templates are in place; attributes drive variant structure and product forms.

**Backend:** One bug was fixed (reorder `attribute_id` → `product_attribute_id`). Gaps: optional DB FKs for product category/brand, missing or unused `display_value`/`color_code` for attribute values, no usage check when deleting attribute values, and pricing templates not auto-applied on save.

**Frontend:** Storefront does not create/update/delete catalog data. Admin has full per-item create/update/delete for products, categories, attributes, attribute values, pricing templates, and brands. “Create all” effectively exists only for **variants** (generate). “Update all” exists for **variants**, **orders**, and **reviews**. “Delete all” exists only for **reviews**; products have backend bulk delete but **no UI**. Attribute values “Bulk import” does not create multiple values from multiple lines; it should be fixed (frontend loop or backend bulk endpoint) or clarified in the UI.
