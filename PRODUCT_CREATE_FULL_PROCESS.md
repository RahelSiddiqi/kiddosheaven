# Product Creation Process Documentation

## Overview

This document provides a complete, step-by-step guide to creating a product in the Kiddo's Heaven e-commerce admin panel. The product creation system supports simple, variable (with variants), and digital products.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Accessing the Product Creation Form](#accessing-the-product-creation-form)
3. [Form Sections & Fields](#form-sections--fields)
4. [Step-by-Step Creation Process](#step-by-step-creation-process)
5. [Product Types](#product-types)
6. [Variant Creation (Variable Products)](#variant-creation-variable-products)
7. [Post-Creation Actions](#post-creation-actions)
8. [API Routes Reference](#api-routes-reference)
9. [Related Files](#related-files)

---

## Prerequisites

Before creating a product, ensure the following are set up:

1. **Categories** - Products must be assigned to a category
    - Create categories via: [`Admin\CategoryController`](app/Http/Controllers/Admin/CategoryController.php)
    - Route: `admin.categories.index` → `/admin/categories`

2. **Brands** (Optional) - Products can have a brand
    - Create brands via: [`Admin\BrandController`](app/Http/Controllers/Admin/BrandController.php)
    - Route: `admin.brands.index` → `/admin/brands`

3. **Product Attributes** (For Variable Products) - Define attributes like Size, Color
    - Manage attributes via: [`Admin\Attribute\AttributeController`](app/Http/Controllers/Admin/Attribute/AttributeController.php)
    - Route: `admin.attributes.index` → `/admin/attributes`
    - Attribute values via: `admin.attributes.values.store` → `/admin/attributes/{attribute}/values`

---

## Accessing the Product Creation Form

**URL:** `/admin/products/create`
**Route Name:** `admin.products.create`
**Controller:** [`ProductController@create`](app/Http/Controllers/Admin/Product/ProductController.php:64)
**View:** [`resources/views/admin/products/create.blade.php`](resources/views/admin/products/create.blade.php)

### Navigation

1. Login to admin panel
2. Go to **Products** from the sidebar menu
3. Click the **Create Product** button

---

## Form Sections & Fields

### 1. Basic Information Section

| Field                                                                      | Required | Type     | Description                  |
| -------------------------------------------------------------------------- | -------- | -------- | ---------------------------- |
| [`name`](resources/views/admin/products/create.blade.php:47)               | Yes      | text     | Product name (max 255 chars) |
| [`category_id`](resources/views/admin/products/create.blade.php:54)        | Yes      | select   | Product category             |
| [`brand_id`](resources/views/admin/products/create.blade.php:66)           | No       | select   | Product brand                |
| [`status`](resources/views/admin/products/create.blade.php:77)             | No       | select   | Active/Inactive              |
| [`sku`](resources/views/admin/products/create.blade.php:88)                | No       | text     | Stock Keeping Unit           |
| [`barcode`](resources/views/admin/products/create.blade.php:96)            | No       | text     | Barcode number               |
| [`product_type`](resources/views/admin/products/create.blade.php:102)      | No       | select   | simple/variable/digital      |
| [`short_description`](resources/views/admin/products/create.blade.php:113) | No       | text     | Brief description (max 500)  |
| [`description`](resources/views/admin/products/create.blade.php:120)       | No       | textarea | Full product description     |

### 2. Product Details Section

| Field                                                                      | Required | Description                           |
| -------------------------------------------------------------------------- | -------- | ------------------------------------- |
| [`features`](resources/views/admin/products/create.blade.php:133)          | No       | Product features (bullet points)      |
| [`care_instructions`](resources/views/admin/products/create.blade.php:139) | No       | How to care for the product           |
| [`ingredients`](resources/views/admin/products/create.blade.php:144)       | No       | Product ingredients (for consumables) |
| [`safety_warning`](resources/views/admin/products/create.blade.php:150)    | No       | Safety warnings                       |

### 3. SEO Section

| Field                                                                     | Required | Description               |
| ------------------------------------------------------------------------- | -------- | ------------------------- |
| [`meta_title`](resources/views/admin/products/create.blade.php:164)       | No       | SEO title (max 255)       |
| [`meta_description`](resources/views/admin/products/create.blade.php:170) | No       | SEO description (max 500) |

### 4. Product Images Section

| Field                                                              | Required | Description                                                 |
| ------------------------------------------------------------------ | -------- | ----------------------------------------------------------- |
| [`images[]`](resources/views/admin/products/create.blade.php:190)  | No       | Multiple image upload (jpeg,png,jpg,gif,webp, max 2MB each) |
| [`video_url`](resources/views/admin/products/create.blade.php:196) | No       | YouTube/Vimeo URL                                           |

### 5. Pricing Section (BDT - Bangladeshi Taka)

| Field                                                                    | Required | Description                         |
| ------------------------------------------------------------------------ | -------- | ----------------------------------- |
| [`price`](resources/views/admin/products/create.blade.php:241)           | Yes      | Selling price                       |
| [`discount_price`](resources/views/admin/products/create.blade.php:249)  | No       | Discounted price                    |
| [`discount_type`](resources/views/admin/products/create.blade.php:256)   | No       | percentage/fixed                    |
| [`cost_price`](resources/views/admin/products/create.blade.php:268)      | No       | Cost price (for profit calculation) |
| [`vat_rate`](resources/views/admin/products/create.blade.php:274)        | No       | VAT percentage (0-100)              |
| [`wholesale_price`](resources/views/admin/products/create.blade.php:282) | No       | Wholesale price for bulk orders     |

### 6. Inventory Section

| Field                                                                    | Required | Description                               |
| ------------------------------------------------------------------------ | -------- | ----------------------------------------- |
| [`stock_quantity`](resources/views/admin/products/create.blade.php:298)  | Yes      | Initial stock count                       |
| [`low_stock_alert`](resources/views/admin/products/create.blade.php:305) | No       | Alert threshold (default: 5)              |
| [`stock_status`](resources/views/admin/products/create.blade.php:313)    | No       | in_stock/out_of_stock/pre_order/backorder |

### 7. Shipping Section

| Field                                                                  | Required | Description             |
| ---------------------------------------------------------------------- | -------- | ----------------------- |
| [`weight`](resources/views/admin/products/create.blade.php:335)        | No       | Weight in kg            |
| [`delivery_type`](resources/views/admin/products/create.blade.php:342) | No       | instant/schedule/frozen |
| [`length`](resources/views/admin/products/create.blade.php:353)        | No       | Length in cm            |
| [`width`](resources/views/admin/products/create.blade.php:358)         | No       | Width in cm             |
| [`height`](resources/views/admin/products/create.blade.php:363)        | No       | Height in cm            |

### 8. Tags & Options Section

| Field                                                                | Required | Description              |
| -------------------------------------------------------------------- | -------- | ------------------------ |
| [`is_featured`](resources/views/admin/products/create.blade.php:376) | No       | Mark as featured product |
| [`tags[]`](resources/views/admin/products/create.blade.php:391)      | No       | Product tags (multiple)  |

### 9. Certifications & Policies Section

| Field                                                                      | Required | Description                    |
| -------------------------------------------------------------------------- | -------- | ------------------------------ |
| [`return_policy`](resources/views/admin/products/create.blade.php:415)     | No       | Return policy text             |
| [`warranty`](resources/views/admin/products/create.blade.php:420)          | No       | Warranty information           |
| [`manufacturer`](resources/views/admin/products/create.blade.php:426)      | No       | Manufacturer name              |
| [`halal_certified`](resources/views/admin/products/create.blade.php:431)   | No       | Halal certification checkbox   |
| [`organic_certified`](resources/views/admin/products/create.blade.php:436) | No       | Organic certification checkbox |

---

## Step-by-Step Creation Process

### Step 1: Navigate to Create Product Page

```
URL: /admin/products/create
Route: admin.products.create
```

### Step 2: Fill Basic Information

1. Enter **Product Name** (required)
2. Select **Category** (required) - This loads category-specific attributes
3. Optionally select **Brand**
4. Choose **Status** (Active/Inactive)
5. Enter **SKU** (or click Generate button)
6. Enter **Barcode** (optional)
7. Select **Product Type** (simple/variable/digital)

### Step 3: Add Descriptions

1. Fill **Short Description** (brief summary)
2. Fill **Description** (detailed information)
3. Add **Features**, **Care Instructions**, etc.

### Step 4: Configure Pricing

1. Enter **Selling Price** (required)
2. Optionally set **Discount Price** and **Discount Type**
3. Enter **Cost Price** (for profit calculations)
4. Set **VAT Rate** (percentage)
5. Set **Wholesale Price** (optional)

### Step 5: Set Inventory

1. Enter **Stock Quantity** (required)
2. Set **Low Stock Alert** threshold
3. Choose **Stock Status**

### Step 6: Configure Shipping

1. Enter **Weight** (kg)
2. Select **Delivery Type** (instant/schedule/frozen)
3. Enter **Dimensions** (L x W x H in cm)

### Step 7: Add Product Images

1. Click the upload area or drag & drop images
2. Supported formats: JPEG, PNG, JPG, GIF, WEBP
3. Maximum file size: 2MB per image
4. Optionally add **Video URL** (YouTube/Vimeo)

### Step 8: Add SEO Information

1. Enter **Meta Title** (for search engines)
2. Enter **Meta Description** (for search engines)

### Step 9: Add Tags & Options

1. Check **Featured Product** if applicable
2. Add **Tags** for better searchability

### Step 10: Set Certifications & Policies

1. Add **Return Policy** text
2. Add **Warranty** information
3. Add **Manufacturer** name
4. Check **Halal Certified** / **Organic Certified** if applicable

### Step 11: Configure Variants (If Variable Product)

If you selected "Variable" as product type:

1. Click **Load Attributes** button
2. Select attribute values (e.g., Size: S, M, L; Color: Red, Blue)
3. Click **Generate Combinations**
4. Edit each variant's SKU, price, and stock

### Step 12: Submit the Form

Click **Create Product** button to save.

---

## Product Types

### Simple Products

- Single product with one price and stock
- No variants
- Fields: [`product_type=simple`](resources/views/admin/products/create.blade.php:104)

### Variable Products

- Multiple variants (e.g., Size + Color combinations)
- Each variant can have different:
    - SKU
    - Price
    - Stock quantity
    - Barcode
- Fields: [`product_type=variable`](resources/views/admin/products/create.blade.php:105)
- Related: [`ProductVariantService`](app/Services/Product/ProductVariantService.php)

### Digital Products

- Downloadable/virtual products
- Fields: [`product_type=digital`](resources/views/admin/products/create.blade.php:106)

---

## Variant Creation (Variable Products)

### Process Flow

1. Select **Variable** as product type
2. Click **Load Attributes** → AJAX call to [`getVariantAttributes`](app/Http/Controllers/Admin/Product/ProductController.php:97)
3. Select attribute values from checkboxes
4. Click **Generate Combinations** → Calls [`ProductVariantService@generateVariants`](app/Services/Product/ProductVariantService.php:24)
5. Edit variant details in the generated table

### Routes for Variant Management

| Action            | Route                                 | Controller Method                                                                                        |
| ----------------- | ------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| List variants     | `admin.products.variants.index`       | [`ProductVariantController@index`](app/Http/Controllers/Admin/Product/ProductVariantController.php)      |
| Create variant    | `admin.products.variants.store`       | [`ProductVariantController@store`](app/Http/Controllers/Admin/Product/ProductVariantController.php)      |
| Update variant    | `admin.products.variants.update`      | [`ProductVariantController@update`](app/Http/Controllers/Admin/Product/ProductVariantController.php)     |
| Delete variant    | `admin.products.variants.destroy`     | [`ProductVariantController@destroy`](app/Http/Controllers/Admin/Product/ProductVariantController.php)    |
| Generate variants | `admin.products.variants.generate`    | [`ProductVariantController@generate`](app/Http/Controllers/Admin/Product/ProductVariantController.php)   |
| Bulk update       | `admin.products.variants.bulk-update` | [`ProductVariantController@bulkUpdate`](app/Http/Controllers/Admin/Product/ProductVariantController.php) |

---

## Post-Creation Actions

### 1. View Product Details

- **URL:** `/admin/products/{product}`
- **Route:** `admin.products.show`
- **Controller:** [`ProductController@show`](app/Http/Controllers/Admin/Product/ProductController.php:105)
- **View:** [`resources/views/admin/products/show.blade.php`](resources/views/admin/products/show.blade.php)

### 2. Edit Product

- **URL:** `/admin/products/{product}/edit`
- **Route:** `admin.products.edit`
- **Controller:** [`ProductController@edit`](app/Http/Controllers/Admin/Product/ProductController.php:140)
- **View:** [`resources/views/admin/products/edit.blade.php`](resources/views/admin/products/edit.blade.php)

### 3. Manage Product Images

| Action      | Route                               |
| ----------- | ----------------------------------- |
| Upload      | `admin.products.images.upload`      |
| Set Primary | `admin.products.images.set-primary` |
| Delete      | `admin.products.images.destroy`     |
| Reorder     | `admin.products.images.reorder`     |

### 4. Manage Stock

- **URL:** `/admin/inventory`
- **Route:** `admin.inventory.index`
- View stock alerts: `admin.inventory.alerts`

### 5. View Inventory Movements

- **URL:** `/admin/inventory/movements/product/{product}`
- **Route:** `admin.inventory.movements.by-product`

---

## API Routes Reference

### Product Routes

| Method    | URI                                             | Name                              | Controller@Method                         |
| --------- | ----------------------------------------------- | --------------------------------- | ----------------------------------------- |
| GET       | `/admin/products`                               | admin.products.index              | ProductController@index                   |
| GET       | `/admin/products/create`                        | admin.products.create             | ProductController@create                  |
| POST      | `/admin/products`                               | admin.products.store              | ProductController@store                   |
| GET       | `/admin/products/{product}`                     | admin.products.show               | ProductController@show                    |
| GET       | `/admin/products/{product}/edit`                | admin.products.edit               | ProductController@edit                    |
| PUT/PATCH | `/admin/products/{product}`                     | admin.products.update             | ProductController@update                  |
| DELETE    | `/admin/products/{product}`                     | admin.products.destroy            | ProductController@destroy                 |
| POST      | `/admin/products/bulk-action`                   | admin.products.bulk-action        | ProductController@bulkAction              |
| GET       | `/admin/products/attributes/{category}`         | admin.products.attributes         | ProductController@getAttributesByCategory |
| GET       | `/admin/products/variant-attributes/{category}` | admin.products.variant-attributes | ProductController@getVariantAttributes    |

### Image Routes

| Method | URI                                            | Name                              |
| ------ | ---------------------------------------------- | --------------------------------- |
| POST   | `/admin/products/{product}/images/upload`      | admin.products.images.upload      |
| POST   | `/admin/products/{product}/images/set-primary` | admin.products.images.set-primary |
| DELETE | `/admin/products/{product}/images/delete`      | admin.products.images.destroy     |
| POST   | `/admin/products/{product}/images/reorder`     | admin.products.images.reorder     |

### Variant Routes

| Method | URI                                              | Name                                |
| ------ | ------------------------------------------------ | ----------------------------------- |
| GET    | `/admin/products/{product}/variants`             | admin.products.variants.index       |
| POST   | `/admin/products/{product}/variants`             | admin.products.variants.store       |
| GET    | `/admin/products/{product}/variants/{variant}`   | admin.products.variants.show        |
| PUT    | `/admin/products/{product}/variants/{variant}`   | admin.products.variants.update      |
| DELETE | `/admin/products/{product}/variants/{variant}`   | admin.products.variants.destroy     |
| POST   | `/admin/products/{product}/variants/generate`    | admin.products.variants.generate    |
| POST   | `/admin/products/{product}/variants/bulk-update` | admin.products.variants.bulk-update |

---

## Related Files

### Controllers

- [`ProductController`](app/Http/Controllers/Admin/Product/ProductController.php) - Main product CRUD
- [`ProductImageController`](app/Http/Controllers/Admin/Product/ProductImageController.php) - Image management
- [`ProductVariantController`](app/Http/Controllers/Admin/Product/ProductVariantController.php) - Variant management
- [`ProductAttributeValueController`](app/Http/Controllers/Admin/Product/ProductAttributeValueController.php) - Attribute values

### Services

- [`ProductService`](app/Services/Product/ProductService.php) - Core product business logic
- [`ProductVariantService`](app/Services/Product/ProductVariantService.php) - Variant generation & management
- [`VariantGeneratorService`](app/Services/VariantGeneratorService.php) - Variant combination generator

### Models

- [`Product`](app/Models/Product.php) - Product model
- [`ProductVariant`](app/Models/ProductVariant.php) - Variant model
- [`ProductAttribute`](app/Models/ProductAttribute.php) - Attribute model
- [`ProductAttributeValue`](app/Models/ProductAttributeValue.php) - Attribute value model
- [`VariantAttribute`](app/Models/VariantAttribute.php) - Variant-attribute relation
- [`Category`](app/Models/Category.php) - Category model
- [`Brand`](app/Models/Brand.php) - Brand model

### Requests (Validation)

- [`StoreProductRequest`](app/Http/Requests/Admin/Product/StoreProductRequest.php) - Create validation
- [`UpdateProductRequest`](app/Http/Requests/Admin/Product/UpdateProductRequest.php) - Update validation

### Views

- [`create.blade.php`](resources/views/admin/products/create.blade.php) - Create form
- [`edit.blade.php`](resources/views/admin/products/edit.blade.php) - Edit form
- [`show.blade.php`](resources/views/admin/products/show.blade.php) - Details view
- [`index.blade.php`](resources/views/admin/products/index.blade.php) - List view

### Database Migrations

- [`2026_01_30_100354_create_products_table.php`](database/migrations/2026_01_30_100354_create_products_table.php)
- [`2026_02_11_000001_create_product_variants_table.php`](database/migrations/2026_02_11_000001_create_product_variants_table.php)
- [`2026_02_11_000002_create_variant_attributes_table.php`](database/migrations/2026_02_11_000002_create_variant_attributes_table.php)
- [`2026_02_08_000001_create_product_attributes_table.php`](database/migrations/2026_02_08_000001_create_product_attributes_table.php)

---

## Validation Rules

All validation is handled by [`StoreProductRequest`](app/Http/Requests/Admin/Product/StoreProductRequest.php:23):

```php
// Required
'name' => 'required|string|max:255'
'category_id' => 'required|exists:categories,id'
'price' => 'required|numeric|min:0'
'stock_quantity' => 'required|integer|min:0'

// Optional
'sku' => 'nullable|string|max:100|unique:products,sku'
'barcode' => 'nullable|string|max:100'
'product_type' => 'nullable|string|in:simple,variable,digital'
'description' => 'nullable|string'
'images' => 'nullable|array'
'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
// ... and more
```

---

## Error Handling

- Form validation errors are displayed at the top of the form
- AJAX requests return JSON with success/error status
- All operations are wrapped in database transactions for data integrity

---

## Notes

- Product slug is auto-generated from the name
- Profit margin is auto-calculated from price and cost_price
- Tags are stored as JSON array
- Images are stored in `storage/app/public/products/`
- Low stock alerts trigger notifications when stock falls below threshold
