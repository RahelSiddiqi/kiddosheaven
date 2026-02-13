# 🚀 Quick Reference - New Features

**Date:** February 12, 2026
**Status:** Production Ready ✅

---

## 📍 Navigation

### Admin Panel URLs

```
/admin/categories          → Category Management (Tree View)
/admin/attributes          → Attribute Management (Inline Editing)
/admin/pricing-templates   → Pricing Templates (Strategy Builder)
/admin/products            → Product List
/admin/products/create     → Create Product (Simplified Form)
/admin/products/{id}       → Product Detail (Inline Variant Editing)
```

---

## 🏷️ Category Management

**Location:** `/admin/categories`

### Features

- **Tree Display:** Hierarchical view with expand/collapse
- **Add Category:** Click "Add Category" → Fill form → Select parent (optional)
- **Edit Category:** Hover → Click edit icon → Update
- **Delete Category:** Hover → Click delete → Confirm → Reassign products
- **Drag-to-Reorder:** Coming soon

### Tips

- Root categories have no parent
- Unlimited nesting levels supported
- Product count shown per category
- Subcategory count shown for parents

---

## 🎨 Attribute Management

**Location:** `/admin/attributes`

### Features

- **Add Attribute:** Click "Add Attribute" → Choose type → Configure
- **Edit Attribute:** Click edit icon → Update settings
- **Add Values:** Click "+ Add Value" → Enter value
- **Edit Values:** Click on value chip → Type new value → Press Enter
- **Reorder Values:** Drag value chips to reorder
- **Delete Values:** Hover chip → Click X

### Attribute Types

- **Text:** Free text input
- **Select:** Dropdown (single choice)
- **Multi-Select:** Multiple choices
- **Color:** Color picker
- **Number:** Numeric input

### Tips

- Mark as "Required" for mandatory fields
- Mark as "Filterable" for shop filters
- Values are reorderable via drag-drop
- Click any value to edit inline (no modal!)

---

## 💰 Pricing Templates

**Location:** `/admin/pricing-templates`

### Quick Start

1. Click "Create Template"
2. Enter name & description
3. Choose pricing strategy
4. Configure strategy settings
5. Mark as "Active" and optionally "Global"
6. Save

### Pricing Strategies

#### 1. Percentage Markup

**Best for:** Standard retail pricing
**Formula:** `Cost × (1 + Percentage/100)`
**Example:** Cost $100 + 50% = Sell $150

**Setup:**

- Enter markup percentage (e.g., 50)
- That's it!

#### 2. Fixed Markup

**Best for:** Low-cost items with consistent margin
**Formula:** `Cost + Fixed Amount`
**Example:** Cost $100 + $10 = Sell $110

**Setup:**

- Enter fixed dollar amount (e.g., 10)
- Simple and predictable

#### 3. Tiered Pricing

**Best for:** Volume-based or cost-range pricing
**Formula:** Different percentages for cost ranges
**Example:**

- $0-$50: 60% markup
- $50-$200: 50% markup
- $200+: 40% markup

**Setup:**

1. Click "+ Add Tier"
2. Enter Min Cost (e.g., 0, 50, 200)
3. Enter Markup % for that tier
4. Add more tiers as needed
5. Higher cost ranges match first

#### 4. Attribute-Based

**Best for:** Size/color/material-specific pricing
**Formula:** Rules based on variant attributes
**Example:**

- Size=Large → 60% markup
- Color=Gold → 70% markup
- Default → 50% markup

**Setup:**

1. Click "+ Add Rule"
2. Enter rule name (e.g., "Large Items")
3. Enter attribute name (e.g., "Size")
4. Enter attribute value (e.g., "Large")
5. Enter markup percentage
6. Add more rules as needed
7. Set default percentage for non-matching variants

### Template Options

- **Active:** Enable/disable template
- **Global:** Apply to all products without specific template
- **Category-Specific:** Attach to specific categories

### Using Templates

1. **Auto-Apply:** Global templates apply automatically
2. **Category-Level:** Attach template to category → All products inherit
3. **Preview:** Click "View & Preview" to see example calculations

---

## 🛍️ Product Management

### Creating Products

**Location:** `/admin/products/create`

**Steps:**

1. Enter basic info (name, slug auto-generates)
2. Select category (hierarchical dropdown)
3. Enter pricing (cost, price, compare at)
4. Set inventory (stock, low stock alert)
5. Upload image
6. Set visibility options
7. Click "Create Product"

**Tips:**

- Slug auto-fills from product name
- Category dropdown shows hierarchy (— indentation)
- All fields validated in real-time

### Managing Variants

**Location:** `/admin/products/{id}`

#### Inline Editing

**Feature:** Edit prices/stock without page reload

**How to Use:**

1. Find variant in table
2. Click on Cost Price, Price, or Stock cell
3. Input appears → Type new value
4. Press Enter or click away to save
5. Spinner shows → Checkmark confirms

**Keyboard Shortcuts:**

- **Enter:** Save changes
- **Escape:** Cancel editing

#### Variant Generator

**Feature:** Create multiple variants from attribute combinations

**How to Use:**

1. Click "Generate Variants" button
2. Select attributes (e.g., Size: S, M, L + Color: Red, Blue)
3. Preview: Shows 6 variants (3×2 combinations)
4. Set base cost price
5. Choose pricing template or manual markup
6. Click "Generate" → All variants created

**Example:**

- Attributes: Size (S, M, L) × Color (Red, Blue, Green)
- Result: 9 variants auto-created with calculated prices

#### Bulk Actions

**Feature:** Update multiple variants at once

**Available Actions:**

1. **Bulk Price Update:**
    - Select variants (checkboxes)
    - Click "Update Prices"
    - Choose: Percentage increase/decrease or Fixed amount
    - Apply to all selected

2. **Bulk Activate/Deactivate:**
    - Select variants
    - Click "Activate" or "Deactivate"
    - Instant status change

3. **Bulk Delete:**
    - Select variants
    - Click "Delete Selected"
    - Confirm deletion
    - All selected variants removed

**Tips:**

- Use "Select All" checkbox for entire list
- Deselect individual items as needed
- Confirmation required for destructive actions

---

## 🎯 Common Workflows

### 1. Setup New Product Category

```
1. /admin/categories → Add Category
2. Name: "Electronics" → Save
3. Add subcategory: "Phones" with parent "Electronics"
4. Add subcategory: "Laptops" with parent "Electronics"
5. Result: Electronics → Phones, Laptops
```

### 2. Create Size Attribute

```
1. /admin/attributes → Add Attribute
2. Name: "Size", Type: "Select"
3. Click "+ Add Value" → Add: S, M, L, XL
4. Drag to reorder if needed
5. Mark as "Required" and "Filterable"
```

### 3. Setup Tiered Pricing Template

```
1. /admin/pricing-templates → Create Template
2. Name: "Volume Pricing"
3. Strategy: "Tiered Pricing"
4. Add tiers:
   - Min $0, 60%
   - Min $50, 50%
   - Min $100, 40%
5. Mark as "Active"
6. Attach to specific categories or make "Global"
```

### 4. Create Product with Variants

```
1. /admin/products/create
2. Fill in: Name, Category, Base Price
3. Save product
4. Go to product detail page
5. Click "Generate Variants"
6. Select: Size (S, M, L) × Color (Red, Blue)
7. Set cost: $50, Choose template: "Standard Retail"
8. Generate → 6 variants created with prices
```

### 5. Bulk Update Variant Prices

```
1. Go to product detail page
2. Check "Select All" or select specific variants
3. Click "Update Prices"
4. Choose "Increase by 10%"
5. Confirm → All selected variants updated
```

### 6. Edit Variant Stock Inline

```
1. Go to product detail page
2. Find variant in table
3. Click on stock quantity cell
4. Type new value (e.g., 100)
5. Press Enter → Saved instantly
```

---

## 🔍 Troubleshooting

### Issue: Can't delete category

**Solution:** Category has products. Reassign products first, then delete.

### Issue: Inline edit not saving

**Solution:** Check browser console for errors. Ensure CSRF token is valid.

### Issue: Pricing template not applying

**Solution:**

1. Check template is marked as "Active"
2. If category-specific, ensure it's attached to the category
3. If global, ensure no category-specific template overrides it

### Issue: Variant generator shows no attributes

**Solution:** Create attributes first in `/admin/attributes` with type "Select" or "Multi-Select"

### Issue: Drag-drop not working

**Solution:** Ensure JavaScript is enabled. Try refreshing the page.

---

## ⚡ Performance Tips

1. **Use Bulk Actions:** Much faster than editing one by one
2. **Inline Editing:** No page reload = instant updates
3. **Pricing Templates:** Apply once, reuse everywhere
4. **Category Hierarchy:** Organize products for easy filtering

---

## 🎓 Best Practices

### Categories

- Keep hierarchy shallow (3-4 levels max)
- Use descriptive names
- Organize logically (Electronics → Phones → Smartphones)

### Attributes

- Create reusable attributes (Size, Color, Material)
- Order values logically (S, M, L, XL not L, XL, S, M)
- Mark common filters as "Filterable"

### Pricing Templates

- Start with "Standard Retail" as global template
- Create category-specific for special cases
- Test calculations with preview before activating
- Name templates clearly (e.g., "Wholesale 30%")

### Variants

- Generate variants early in product creation
- Use consistent attribute naming across products
- Keep variant count reasonable (<50 per product)
- Use bulk actions for efficiency

---

## 📞 Need Help?

- Check main documentation: `FEATURES-COMPLETE.md`
- Review user guide: `QUICK-START-GUIDE.md`
- Inspect code comments for technical details
- Check browser console for JavaScript errors

---

**Last Updated:** February 12, 2026
**Version:** 1.0 (Complete)
**All Features:** ✅ Operational
