# Variable Products & Purchase Cost Management

**Date:** 2025-02-14  
**Question:** Can we manage variable products with different sizes/prices and products bought at different prices over time (e.g., March @ 100, next month lower)?

---

## ✅ YES — The System Fully Supports Both Scenarios

---

## 1. Variable Products with Different Prices Per Size/Variant

### How It Works

**Product Variants** (`product_variants` table) allow each size/color/attribute combination to have its own:
- **Selling Price** (`price`) — different per variant
- **Cost Price** (`cost_price`) — can differ per variant
- **Compare At Price** (`compare_at_price`) — for showing discounts

### Example: T-Shirt with Sizes

```
Product: "Cotton T-Shirt"
├── Variant: Small (S)
│   ├── Price: ৳500
│   ├── Cost: ৳280
│   └── Stock: 20
├── Variant: Medium (M)
│   ├── Price: ৳500
│   ├── Cost: ৳280
│   └── Stock: 15
└── Variant: Large (L)
    ├── Price: ৳550  ← Different price!
    ├── Cost: ৳300
    └── Stock: 10
```

### Implementation Details

1. **Variant Creation:**
   - Each `ProductVariant` has its own `price` field
   - When generating variants, you can:
     - Set different prices per variant manually
     - Use `price_modifier` from attribute values (e.g., Large adds +50)
     - Set defaults that apply to all variants, then adjust individually

2. **Code Location:**
   - **Model:** `app/Models/ProductVariant.php` (lines 20-21: `price`, `cost_price`)
   - **Service:** `app/Services/VariantGeneratorService.php` (line 95: price calculation with modifiers)
   - **UI:** Product show page → Variants section → Edit each variant's price

3. **Price Modifiers:**
   - Attribute values can have `price_modifier` (e.g., +50 for Large size)
   - When generating variants, system adds: `base_price + sum(price_modifiers)`

### ✅ **Answer:** Yes, you can have different prices for different sizes/variants.

---

## 2. Products Bought at Different Prices Over Time

### How It Works

**Purchase Batches** (`purchase_batches` table) track each purchase with:
- **Unit Cost** (`unit_cost`) — purchase price for that batch
- **Purchase Date** (`purchase_date`) — when bought
- **Quantity Received** (`quantity_received`)
- **Remaining Quantity** (`remaining_quantity`) — for FIFO/LIFO

### Example: Rice Purchased Over Time

```
Product: "Premium Rice 5kg"
├── Batch 1 (March 2025)
│   ├── Purchase Date: 2025-03-01
│   ├── Unit Cost: ৳100
│   ├── Quantity: 100 bags
│   └── Remaining: 30 bags
├── Batch 2 (April 2025)
│   ├── Purchase Date: 2025-04-15
│   ├── Unit Cost: ৳95  ← Lower price!
│   ├── Quantity: 150 bags
│   └── Remaining: 120 bags
└── Batch 3 (May 2025)
    ├── Purchase Date: 2025-05-10
    ├── Unit Cost: ৳90  ← Even lower!
    ├── Quantity: 200 bags
    └── Remaining: 200 bags
```

### Cost Calculation Methods

The system supports **three costing methods:**

1. **Weighted Average Cost** (default)
   - Calculates: `(sum of all batch costs × remaining quantities) / total remaining quantity`
   - Example: `(100×30 + 95×120 + 90×200) / 350 = ৳92.57`
   - **Code:** `ProductVariant::calculateAverageCost()` (line 163)

2. **FIFO (First In, First Out)**
   - When selling, uses oldest batch first
   - **Code:** `InventoryService::deductStock()` (line 85)

3. **LIFO (Last In, First Out)**
   - When selling, uses newest batch first
   - **Code:** `InventoryService` supports both methods

### Implementation Details

1. **Creating Purchase Batches:**
   - **Route:** `admin.purchase-batches.store`
   - **Controller:** `PurchaseBatchController::store()` (line 54)
   - **Required:** `product_id`, `unit_cost`, `quantity`, `purchase_date`
   - **Optional:** `product_variant_id` (for variant-specific batches)

2. **Multiple Batches Per Product/Variant:**
   - You can create unlimited batches for the same product/variant
   - Each batch has its own `unit_cost` and `purchase_date`
   - System tracks `remaining_quantity` per batch

3. **Automatic Cost Updates:**
   - When creating a batch, system can update variant's `cost_price` to weighted average
   - **Method:** `ProductVariant::updateAverageCost()` (line 182)

4. **Cost History:**
   - **Route exists:** `admin.reports.cost-history` (but controller method not implemented yet)
   - Purchase batches store all historical costs
   - Can query: `PurchaseBatch::where('product_id', $id)->orderBy('purchase_date')->get()`

### ✅ **Answer:** Yes, you can track products bought at different prices over time. Each purchase creates a batch with its own cost and date.

---

## 3. How They Work Together

### Scenario: Variable Product with Different Purchase Costs

**Example: T-Shirt in Large Size**

```
Product Variant: "Cotton T-Shirt - Large"
├── Selling Price: ৳550 (set per variant)
└── Purchase Batches:
    ├── Batch 1 (March)
    │   ├── Unit Cost: ৳100
    │   └── Remaining: 10 units
    ├── Batch 2 (April)
    │   ├── Unit Cost: ৳95
    │   └── Remaining: 20 units
    └── Batch 3 (May)
        ├── Unit Cost: ৳90
        └── Remaining: 15 units

Current Average Cost: (100×10 + 95×20 + 90×15) / 45 = ৳94.44
```

### How Orders Use Batches

When a customer orders:
1. System uses **FIFO** (or LIFO) to deduct stock from batches
2. Records which batch was used in `order_items.unit_cost`
3. Calculates profit: `selling_price - batch_unit_cost`

---

## 4. Current System Capabilities

### ✅ What's Working

| Feature | Status | Location |
|---------|--------|----------|
| **Different prices per variant** | ✅ Fully supported | `ProductVariant.price` |
| **Price modifiers from attributes** | ✅ Supported | `ProductAttributeValue.price_modifier` |
| **Multiple purchase batches** | ✅ Fully supported | `PurchaseBatch` model |
| **Different costs per batch** | ✅ Fully supported | `PurchaseBatch.unit_cost` |
| **Purchase date tracking** | ✅ Fully supported | `PurchaseBatch.purchase_date` |
| **Weighted average cost** | ✅ Calculated automatically | `ProductVariant::calculateAverageCost()` |
| **FIFO stock deduction** | ✅ Implemented | `InventoryService::deductStock()` |
| **LIFO support** | ✅ Available | `InventoryService` (configurable) |
| **Variant-specific batches** | ✅ Supported | `PurchaseBatch.product_variant_id` |

### ⚠️ What's Missing / Could Be Improved

| Feature | Status | Recommendation |
|---------|--------|----------------|
| **Cost History Report** | ⚠️ Route exists, method not implemented | Implement `ReportController::costHistory()` to show purchase cost trends over time |
| **Batch Cost Comparison View** | ❌ Not available | Add view showing all batches for a product/variant with cost comparison |
| **Automatic Cost Price Update** | ⚠️ Manual call | Consider auto-updating variant `cost_price` when batch is created |
| **Price History (Selling Price)** | ❌ Not tracked | Consider adding `price_history` table if you need to track selling price changes over time |

---

## 5. How to Use

### Creating Variants with Different Prices

1. **Via Variant Generator:**
   - Go to Product → Show → Variants section
   - Click "Generate Variants"
   - Select attributes (e.g., Size: S, M, L)
   - Set base price (e.g., 500)
   - System creates variants; edit each variant's price individually

2. **Manually:**
   - Product → Show → Variants → Add Variant
   - Set `price` per variant (e.g., S=500, M=500, L=550)

3. **Using Price Modifiers:**
   - Edit attribute values (e.g., Size "Large")
   - Set `price_modifier` = +50
   - When generating variants, Large will automatically be 550

### Recording Purchases at Different Costs

1. **Create Purchase Batch:**
   - Go to **Inventory → Purchase Batches → Create**
   - Select Product (and Variant if applicable)
   - Enter:
     - **Unit Cost:** ৳100 (March purchase)
     - **Quantity:** 100
     - **Purchase Date:** 2025-03-01
   - Save

2. **Next Month Purchase:**
   - Create another batch for same product/variant
   - **Unit Cost:** ৳95 (April purchase)
   - **Quantity:** 150
   - **Purchase Date:** 2025-04-15
   - System tracks both batches separately

3. **View Batches:**
   - Product → Show → Purchase Batches section
   - Or: Inventory → Purchase Batches → Filter by product

---

## 6. Database Schema

### Product Variants
```sql
product_variants
├── id
├── product_id
├── price (DECIMAL) ← Different per variant
├── cost_price (DECIMAL) ← Can differ per variant
└── ...
```

### Purchase Batches
```sql
purchase_batches
├── id
├── product_id
├── product_variant_id (nullable) ← Links to specific variant
├── unit_cost (DECIMAL) ← Purchase price for this batch
├── purchase_date (DATE) ← When purchased
├── quantity_received (INT)
├── remaining_quantity (INT) ← For FIFO/LIFO
└── ...
```

---

## 7. Recommendations

1. **Implement Cost History Report:**
   - Add `ReportController::costHistory()` method
   - Show chart/table of purchase costs over time per product/variant
   - Useful for seeing price trends

2. **Add Batch Comparison View:**
   - On product/variant show page, add section showing all batches
   - Compare costs: "March: ৳100, April: ৳95, May: ৳90"
   - Show remaining stock per batch

3. **Auto-Update Cost Price:**
   - When creating a batch, optionally call `variant->updateAverageCost()`
   - Keeps variant `cost_price` in sync with batches

4. **Consider Price History (Optional):**
   - If you need to track selling price changes over time
   - Add `product_price_history` table
   - Log when `ProductVariant.price` changes

---

## 8. Conclusion

**✅ YES — Your system fully supports:**

1. **Variable products with different prices per size/variant** ✅
   - Each variant has its own `price`
   - Can use price modifiers from attributes
   - Can set prices individually

2. **Products bought at different prices over time** ✅
   - Each purchase creates a `PurchaseBatch` with its own `unit_cost` and `purchase_date`
   - System tracks multiple batches per product/variant
   - Calculates weighted average cost automatically
   - Uses FIFO/LIFO for stock deduction

**The system is production-ready for both scenarios.** The only missing piece is a **Cost History Report** UI (route exists, method needs implementation) to visualize purchase cost trends over time.

---

## 9. Restricted Combinations (Size-Specific Colors)

**Question:** Small has Red, Green, Blue (same price); Large has Blue, Yellow; Medium has only Red. Can we manage that?

### ✅ YES — With a Small Addition

The **data model already supports it.** Each variant is just a set of attribute values (e.g. Size=Small + Color=Red). There is no rule that “every size must have every color.” So you can have exactly:

- Small – Red  
- Small – Green  
- Small – Blue  
- Medium – Red  
- Large – Blue  
- Large – Yellow  

(same price for Small’s three colors; different prices for Medium/Large if you want).

### Current Limitation

The **“Generate Variants”** button creates **all** combinations of the attributes you select (Cartesian product). So if you select Size S,M,L and Color Red,Green,Blue,Yellow you get 12 variants, not 6.

### Ways to Do It Today

1. **Generate all, then delete**  
   Generate all 12 variants, then delete the 6 you don’t want (e.g. M-Green, M-Blue, M-Yellow, L-Red, L-Green, S-Yellow). Set the same price for Small’s three; set prices for Medium and Large as needed.

2. **Custom combinations (recommended)**  
   Use the new **“Create only these combinations”** flow (see below): you choose exactly which (Size, Color) pairs to create (e.g. the 6 above). No need to generate 12 and delete 6.

### Summary

| What you want | Supported? | How |
|---------------|------------|-----|
| Small: Red, Green, Blue (same price) | ✅ Yes | Create only those 3 variants; set same price. |
| Large: Blue, Yellow only | ✅ Yes | Create only those 2 variants. |
| Medium: Red only | ✅ Yes | Create 1 variant (Medium + Red). |
| Different price per size/color | ✅ Yes | Edit each variant’s price after creation. |

So: **yes, you can manage “small has red/green/blue, large has blue/yellow, medium has red”** in the existing system; the only change is how variants are created (custom combinations instead of full matrix).
