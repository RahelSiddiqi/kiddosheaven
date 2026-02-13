# Product & Variant Management Guide

## 🎯 Simple & Intuitive Product Creation

### Creating a Simple Product (No Variants)

1. **Navigate to Products** → Click "Add Product"
2. **Fill Basic Information**:
    - Product Name (required)
    - Category (required)
    - Brand (optional)
    - SKU, Barcode (optional - auto-generated if empty)
    - Product Type: Keep "Simple"

3. **Set Pricing**:
    - Price (৳) - Selling price
    - Cost Price (৳) - Your purchase cost
    - Profit margin is calculated automatically

4. **Add Stock**: Enter initial stock quantity
5. **Upload Images**: Drag & drop or click to upload
6. **Click "Create Product"** ✅

---

### Creating a Variable Product (With Variants)

**Example**: T-Shirt with 3 colors × 3 sizes = 9 variants

#### Step-by-Step:

1. **Basic Information**: Fill product name, category, brand
2. **Product Type**: Select "**Variable**"
3. **Configure Base Price**: Enter base price (e.g., 500 ৳)

#### Variants Section Appears Automatically:

4. **Step 1: Choose Variant Attributes**
    - You'll see checkboxes for attributes configured in your category
    - Example: ☑️ Color, ☑️ Size
    - System shows: "Will create 9 variants" (3 colors × 3 sizes)

5. **Step 2: Click "Generate Variants"**
    - System creates all combinations automatically
    - Red-Small, Red-Medium, Red-Large
    - Blue-Small, Blue-Medium, Blue-Large
    - Green-Small, Green-Medium, Green-Large

6. **Step 3: Edit Variant Details in Table**
    - **SKU**: Auto-generated (e.g., RED-SMA), can be edited
    - **Price**: Base price + attribute modifiers
    - **Cost**: Set cost price for each variant
    - **Stock**: Set initial stock (default: 0)
    - **Barcode**: Optional, for scanning
    - **Default**: Select which variant is the default (first one by default)
    - **Active**: Check to make variant available for sale

#### Quick Bulk Actions:

- **Copy Price to All**: Uses first variant's price for all
- **Copy Cost to All**: Uses first variant's cost for all
- **Copy Stock to All**: Uses first variant's stock for all

7. **Click "Create Product"** ✅

---

## 🏷️ Setting Up Attributes for Variants

Before creating variable products, configure your category attributes:

1. **Navigate to**: Catalogs → Select Category (e.g., "Clothing")
2. **Configure Attributes**:
    - **Color** (for variants):
        - Type: Select
        - ☑️ Use for Variants
        - Values: Red, Blue, Green, etc.

    - **Size** (for variants):
        - Type: Select
        - ☑️ Use for Variants
        - Values: Small, Medium, Large, XL

    - **Weight** (for variants - optional):
        - Type: Select
        - ☑️ Use for Variants
        - Values: 0.5kg, 1kg, 2kg

3. **Save** - Now products in this category can use these for variants

---

## 📦 Managing Inventory Per Variant

### Adding Stock (Purchase Batch):

Coming soon: Purchase Batch Management

- Will track: Which batch, Unit cost per batch, Expiry date
- Links to specific variant (e.g., Red-Small from Batch #001)
- FIFO tracking: Sells oldest stock first

**Current**: Set stock quantity directly in variant table during product creation

---

## 💡 Example Scenarios

### Scenario 1: Basic Product (Toy)

- **Type**: Simple
- **No variants needed**
- Just set: Price, Cost, Stock, Images
- Done in 2 minutes ✅

### Scenario 2: Clothing with Color & Size

- **Type**: Variable
- **Variants**: Color (4) × Size (5) = 20 variants
- **Steps**:
    1. Select "Variable" product type
    2. Check: Color, Size
    3. Click "Generate Variants"
    4. Bulk apply base price
    5. Adjust specific variant prices if needed
    6. Done in 5 minutes ✅

### Scenario 3: Food with Multiple Weights

- **Type**: Variable
- **Variants**: Weight (3 options)
- **Example**: Rice Pack - 0.5kg, 1kg, 2kg
- **Steps**:
    1. Select "Variable" product type
    2. Check: Weight
    3. Generate 3 variants
    4. Set different prices: 50৳, 95৳, 180৳
    5. Set different costs for profit tracking
    6. Done in 3 minutes ✅

### Scenario 4: Complex Multi-Attribute

- **Type**: Variable
- **Variants**: Color (3) × Size (4) × Weight (2) = 24 variants
- **Steps**:
    1. Select all 3 attributes
    2. System creates all 24 combinations
    3. Use bulk actions to set base values
    4. Adjust outliers (e.g., XL-2kg costs more)
    5. Done in 7 minutes ✅

---

## 🎨 UX Features

### ✅ What Makes This Easy:

1. **Auto-Generation**: No manual variant creation
2. **Bulk Actions**: Apply values to all variants at once
3. **Visual Feedback**: See variant count before generating
4. **Smart Defaults**: SKUs auto-generated, first variant is default
5. **Inline Editing**: Edit all variants in one table view
6. **No Page Reloads**: Everything happens instantly

### ✅ Smart Behaviors:

- Product type "Simple" → No variant section shows
- Product type "Variable" → Variant section appears
- Select category first → Only that category's variant attributes show
- Attribute modifiers → Auto-applied to variant prices
- Default variant → First one selected, can change with radio button

---

## 📊 What's Next

### Phase 2 (In Development):

- **Purchase Batch Management**: Track cost per batch
- **FIFO Stock Deduction**: Sell oldest stock first
- **Cost Tracking**: Know which batch each sale came from
- **Profit Reports**: Accurate profit per variant, per batch

### Phase 3 (Planned):

- **Variant Images**: Upload image per variant
- **Low Stock Alerts**: Per variant
- **Reorder Points**: Auto-suggest when to restock
- **Variant Performance**: See which variants sell best

---

## 🆘 Troubleshooting

**Q: I don't see variant attributes?**

- A: Configure attributes in your category first, enable "Use for Variants"

**Q: Can I change variants after creation?**

- A: Yes, edit product → Variant section → Add/remove/modify

**Q: Can I have different prices per variant?**

- A: Yes! Set individual prices in the variant table

**Q: Can I track different costs per batch?**

- A: Coming in Phase 2 (Purchase Batch Management)

**Q: How do I disable a variant without deleting?**

- A: Uncheck "Active" checkbox for that variant

---

## 🎯 Best Practices

1. **Set up attributes first** before creating products
2. **Use bulk actions** to save time on similar variants
3. **Set accurate cost prices** for profit tracking
4. **Use SKU format**: Consistent naming (e.g., TSHIRT-RED-SMA)
5. **Mark default variant**: Most popular color/size combination
6. **Deactivate instead of delete**: Keeps sales history

---

Need help? Check documentation or contact support.
