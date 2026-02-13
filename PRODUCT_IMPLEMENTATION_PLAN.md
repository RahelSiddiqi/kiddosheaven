# 🎯 Product System Implementation - Action Plan

**Date**: February 11, 2026
**Status**: Ready for Implementation
**Priority**: 🔴 Critical

---

## ✅ What We Have Done

### 1. Comprehensive Analysis ✅

- Created `PRODUCT_SYSTEM_ANALYSIS.md` with complete gap analysis
- Identified all missing features for proper variant management
- Documented inventory and cost tracking issues
- Analyzed catalog/attribute system structure

### 2. Database Migrations Created ✅

- `2026_02_11_000001_create_product_variants_table.php` ✅
- `2026_02_11_000002_create_variant_attributes_table.php` ✅
- `2026_02_11_000003_create_inventory_items_table.php` ✅
- `2026_02_11_000004_update_purchase_batches_add_variant.php` ✅
- `2026_02_11_000005_update_inventory_movements_add_variant.php` ✅
- `2026_02_11_000006_update_order_items_add_variant_cost.php` ✅

### 3. Models Created ✅

- `app/Models/ProductVariant.php` - Full featured with stock management
- `app/Models/VariantAttribute.php` - Links variants to attributes
- `app/Models/InventoryItem.php` - Connects variants to batches

### 4. Services Created ✅

- `app/Services/VariantGeneratorService.php` - Generates all variant combinations

### 5. Documentation ✅

- Comprehensive analysis document
- This action plan
- Clear next steps defined

---

## 🚨 Critical Issues Found

### Issue 1: No Proper Variant System

**Problem**: Variants stored as JSON, can't query or track inventory per variant
**Impact**: Can't manage "Red T-Shirt Size Small" vs "Blue T-Shirt Size Large" separately
**Solution**: ✅ Created relational tables (product_variants, variant_attributes)

### Issue 2: Cost Tracking Not Per Variant

**Problem**: If you buy at 100 BDT today, 120 BDT next week, system can't differentiate by variant
**Impact**: Wrong COGS calculation, wrong profit margins
**Solution**: ✅ Added product_variant_id to purchase_batches

### Issue 3: Multi-Attribute Variants Not Possible

**Problem**: UI can't handle Red×Blue×Green + Small×Medium×Large + 0.5kg×1kg×2kg
**Impact**: Can't sell products with multiple variation axes
**Solution**: ✅ VariantGeneratorService with recursive combination logic

### Issue 4: No Variant-Level Inventory

**Problem**: Stock tracked at product level, not variant level
**Impact**: Shows "10 in stock" but all 10 might be Size Small (none in Large)
**Solution**: ✅ ProductVariant has stock_quantity field + inventory_items table

---

## 📋 What's Left to Do

### Phase 1: Database Setup (Next 2 hours)

```bash
# 1. Run migrations
php artisan migrate

# 2. Verify tables created
php artisan tinker
> Schema::hasTable('product_variants')  // should be true
> Schema::hasTable('variant_attributes')  // should be true
> Schema::hasTable('inventory_items')  // should be true

# 3. Check relationships
> $variant = new App\Models\ProductVariant;
> $variant->product  // should work
> $variant->variantAttributes  // should work
```

**Status**: ⏳ Ready to execute

---

### Phase 2: Update Product Model (1 hour)

**File**: `app/Models/Product.php`

**Add these relationships:**

```php
public function variants()
{
    return $this->hasMany(ProductVariant::class);
}

public function defaultVariant()
{
    return $this->hasOne(ProductVariant::class)->where('is_default', true);
}

public function activeVariants()
{
    return $this->hasMany(ProductVariant::class)->where('is_active', true);
}
```

**Status**: ⏳ Not started

---

### Phase 3: Restructure Create Form (4-6 hours) 🔴 CRITICAL

**File**: `resources/views/admin/products/create.blade.php`

**Current State:**

- ❌ Line 193-203: "Variants Engine" section only shows placeholder text
- ❌ No UI to select which attributes create variants
- ❌ No variant bulk editor

**What to Build:**

#### 1. Variant Attribute Selector (Add after Product Type field)

```blade
<div id="variant-configurator" style="display: none;">
    <label class="text-sm font-medium">Select Variant Attributes</label>
    <p class="text-xs text-gray-500">Choose attributes that create variants (e.g., Color, Size, Weight)</p>

    <div id="variant-attribute-options">
        <!-- Loaded via AJAX when catalog selected -->
        <!-- Checkboxes for each attribute with 2+ values -->
    </div>

    <button type="button" onclick="generateVariantCombinations()"
        class="btn btn-primary">
        Generate Variants
    </button>

    <div id="variant-count-preview">
        <!-- Shows: "3 colors × 4 sizes = 12 variants will be created" -->
    </div>
</div>
```

#### 2. Variant Bulk Editor (Replace placeholder in Variants Engine section)

```blade
<div id="variants-table" style="display: none;">
    <table class="w-full">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Attributes</th>
                <th>Price</th>
                <th>Cost</th>
                <th>Stock</th>
                <th>Barcode</th>
                <th>Weight</th>
                <th>Default</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="variants-tbody">
            <!-- Rows added dynamically by JavaScript -->
        </tbody>
    </table>

    <div class="mt-4 flex gap-2">
        <button type="button" onclick="applyPriceToAll()">Apply Price to All</button>
        <button type="button" onclick="applyCostToAll()">Apply Cost to All</button>
        <button type="button" onclick="applyStockToAll()">Apply Stock to All</button>
    </div>
</div>
```

#### 3. JavaScript Functions Needed

```javascript
// Load variant attribute options when catalog selected
function loadVariantAttributes(catalogId) {
    fetch(`/admin/products/variant-attributes/${catalogId}`)
        .then((r) => r.json())
        .then((data) => {
            // Render checkboxes for each attribute
            // Show value count (e.g., "Color (3 values)")
        });
}

// Generate variant combinations
function generateVariantCombinations() {
    const selected = getSelectedAttributes();
    const count = calculateCombinations(selected);

    if (count > 100) {
        alert("Too many variants! Select fewer attributes.");
        return;
    }

    // Generate permutations
    const combinations = generatePermutations(selected);
    renderVariantTable(combinations);
}

// Render variant editor table
function renderVariantTable(combinations) {
    const tbody = document.getElementById("variants-tbody");
    tbody.innerHTML = "";

    combinations.forEach((combo, index) => {
        const row = createVariantRow(combo, index);
        tbody.appendChild(row);
    });

    document.getElementById("variants-table").style.display = "block";
}

// Create single variant row
function createVariantRow(combo, index) {
    const attributeNames = combo.map((c) => c.value).join(" - ");
    const skuParts = combo
        .map((c) => c.value.substring(0, 3).toUpperCase())
        .join("-");

    return `
        <tr data-index="${index}">
            <td><input name="variants[${index}][sku]" value="PROD-${skuParts}" /></td>
            <td>${attributeNames}</td>
            <td><input type="number" name="variants[${index}][price]" value="${basePrice}" /></td>
            <td><input type="number" name="variants[${index}][cost_price]" value="0" /></td>
            <td><input type="number" name="variants[${index}][stock]" value="0" /></td>
            <td><input name="variants[${index}][barcode]" /></td>
            <td><input type="number" name="variants[${index}][weight]" /></td>
            <td><input type="radio" name="default_variant" value="${index}" /></td>
            <td><button type="button" onclick="deleteVariantRow(${index})">Delete</button></td>
        </tr>
    `;
}

// Bulk actions
function applyPriceToAll() {
    const price = prompt("Enter price for all variants:");
    if (price) {
        document.querySelectorAll('input[name$="[price]"]').forEach((input) => {
            input.value = price;
        });
    }
}
```

**Status**: ⏳ Not started

---

### Phase 4: Update ProductController (2-3 hours)

**File**: `app/Http/Controllers/Admin/Product/ProductController.php`

**Update store() method to handle variants:**

```php
public function store(Request $request)
{
    DB::transaction(function () use ($request) {
        // 1. Create product
        $product = Product::create($request->validated());

        // 2. If product_type is 'variable' and variants provided
        if ($request->product_type === 'variable' && $request->has('variants')) {
            foreach ($request->variants as $variantData) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'],
                    'price' => $variantData['price'],
                    'cost_price' => $variantData['cost_price'],
                    'stock_quantity' => $variantData['stock'] ?? 0,
                    'barcode' => $variantData['barcode'] ?? null,
                    'weight' => $variantData['weight'] ?? null,
                    'is_default' => $variantData['is_default'] ?? false,
                ]);

                // Link variant to attributes
                foreach ($variantData['attributes'] as $attrId => $valueId) {
                    VariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $attrId,
                        'product_attribute_value_id' => $valueId,
                    ]);
                }
            }
        }

        return $product;
    });

    return redirect()->route('admin.products.index')
        ->with('success', 'Product created with variants!');
}
```

**Add new route for variant attributes API:**

```php
// In routes/admin.php
Route::get('/products/variant-attributes/{catalog}', [ProductController::class, 'getVariantAttributes']);
```

**Add controller method:**

```php
public function getVariantAttributes(Catalog $catalog)
{
    return (new VariantGeneratorService())->getVariantAttributeOptions($catalog);
}
```

**Status**: ⏳ Not started

---

### Phase 5: Purchase Batch Management (3-4 hours)

**New File**: `app/Http/Controllers/Admin/PurchaseBatchController.php`

**Create CRUD for purchase batches:**

```php
class PurchaseBatchController extends Controller
{
    public function index()
    {
        $batches = PurchaseBatch::with('product', 'variant')
            ->latest()
            ->paginate(50);

        return view('admin.inventory.batches.index', compact('batches'));
    }

    public function create()
    {
        $products = Product::active()->get();
        return view('admin.inventory.batches.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'unit_cost' => 'required|numeric|min:0',
            'quantity_received' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
            'supplier' => 'nullable|string',
            'expiry_date' => 'nullable|date',
        ]);

        $batch = PurchaseBatch::create($validated);

        // Create inventory item
        InventoryItem::create([
            'product_id' => $batch->product_id,
            'product_variant_id' => $batch->product_variant_id,
            'purchase_batch_id' => $batch->id,
            'quantity_on_hand' => $batch->quantity_received,
            'unit_cost' => $batch->unit_cost,
            'location' => 'main',
        ]);

        // Update variant stock
        if ($batch->product_variant_id) {
            $variant = ProductVariant::find($batch->product_variant_id);
            $variant->addStock($batch->quantity_received);
            $variant->updateAverageCost();
        }

        return redirect()->route('admin.batches.index')
            ->with('success', 'Purchase batch recorded!');
    }
}
```

**Create views:**

- `resources/views/admin/inventory/batches/index.blade.php`
- `resources/views/admin/inventory/batches/create.blade.php`

**Add routes:**

```php
Route::resource('batches', PurchaseBatchController::class);
```

**Status**: ⏳ Not started

---

### Phase 6: Order Processing with FIFO (4-5 hours)

**Create Service**: `app/Services/FIFOStockService.php`

```php
class FIFOStockService
{
    /**
     * Deduct stock using FIFO (oldest batch first)
     */
    public function deductStock(ProductVariant $variant, int $quantity): array
    {
        $inventoryItems = InventoryItem::where('product_variant_id', $variant->id)
            ->whereRaw('quantity_on_hand - quantity_reserved > 0')
            ->orderBy('created_at', 'asc')  // FIFO
            ->get();

        $remaining = $quantity;
        $movements = [];

        foreach ($inventoryItems as $item) {
            if ($remaining <= 0) break;

            $available = $item->quantity_on_hand - $item->quantity_reserved;
            $toDeduct = min($remaining, $available);

            // Deduct from inventory item
            $item->deduct($toDeduct);

            // Record movement
            $movements[] = InventoryMovement::create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'inventory_item_id' => $item->id,
                'purchase_batch_id' => $item->purchase_batch_id,
                'movement_type' => InventoryMovement::TYPE_SALE,
                'quantity' => -$toDeduct,
                'unit_cost' => $item->unit_cost,
            ]);

            $remaining -= $toDeduct;
        }

        if ($remaining > 0) {
            throw new \Exception("Insufficient stock! Need {$quantity}, have " . ($quantity - $remaining));
        }

        // Update variant stock
        $variant->deduct($quantity);

        return $movements;
    }
}
```

**Update OrderController to use FIFO:**

```php
public function store(Request $request)
{
    $fifoService = new FIFOStockService();

    DB::transaction(function () use ($request, $fifoService) {
        $order = Order::create([...]);

        foreach ($request->items as $itemData) {
            $variant = ProductVariant::find($itemData['variant_id']);

            // Use FIFO to deduct stock and get movements
            $movements = $fifoService->deductStock($variant, $itemData['quantity']);

            // Calculate weighted average COGS
            $totalCost = collect($movements)->sum(fn($m) => abs($m->quantity) * $m->unit_cost);
            $avgCost = $totalCost / $itemData['quantity'];

            // Create order item with COGS
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity' => $itemData['quantity'],
                'price' => $variant->price,
                'unit_cost' => $avgCost,  // For profit calculation
                'purchase_batch_id' => $movements[0]->purchase_batch_id ?? null,
            ]);
        }
    });
}
```

**Status**: ⏳ Not started

---

## 🎯 Priority Order

### 🔴 Critical (Must Do First)

1. ✅ Run migrations (Phase 1) - **DO THIS NOW**
2. ⏳ Update Product model relationships (Phase 2) - **30 min**
3. ⏳ Restructure create form (Phase 3) - **HIGHEST PRIORITY, 4-6 hours**
4. ⏳ Update ProductController (Phase 4) - **2-3 hours**

### 🟡 Important (Do This Week)

5. ⏳ Purchase Batch Management (Phase 5) - **3-4 hours**
6. ⏳ FIFO Stock Service (Phase 6) - **4-5 hours**

### 🟢 Enhancement (Do Next Week)

7. ⏳ Variant images upload
8. ⏳ Barcode generation
9. ⏳ Stock alerts per variant
10. ⏳ Inventory reports

---

## 📊 Testing Checklist

After completing Phases 1-4, test with this scenario:

### Test Case: T-Shirt with Variants

```
Product: "Cotton T-Shirt"
Attributes:
  - Color: Red, Blue, Green (3 values)
  - Size: Small, Medium, Large (3 values)

Expected Result: 3 × 3 = 9 variants

Variants Generated:
1. Red - Small    | SKU: TSHIRT-RED-SM  | Price: 100 | Stock: 10
2. Red - Medium   | SKU: TSHIRT-RED-MD  | Price: 110 | Stock: 15
3. Red - Large    | SKU: TSHIRT-RED-LG  | Price: 120 | Stock: 8
4. Blue - Small   | SKU: TSHIRT-BLU-SM  | Price: 100 | Stock: 12
5. Blue - Medium  | SKU: TSHIRT-BLU-MD  | Price: 110 | Stock: 20
6. Blue - Large   | SKU: TSHIRT-BLU-LG  | Price: 120 | Stock: 5
7. Green - Small  | SKU: TSHIRT-GRN-SM  | Price: 100 | Stock: 7
8. Green - Medium | SKU: TSHIRT-GRN-MD  | Price: 110 | Stock: 18
9. Green - Large  | SKU: TSHIRT-GRN-LG  | Price: 120 | Stock: 3

Test Operations:
☐ Create product with variants
☐ Add purchase batch for "Red - Small" at 80 BDT cost
☐ Add purchase batch for "Red - Small" at 90 BDT cost (price change)
☐ Place order for 5 × "Red - Small"
☐ Verify FIFO used (4 from first batch @ 80, 1 from second @ 90)
☐ Check COGS = (4×80 + 1×90) / 5 = 82 BDT
☐ Verify stock decreased correctly
☐ Check profit = (5 × 100) - (5 × 82) = 90 BDT
```

---

## 🚀 Immediate Next Steps (Today)

```bash
# 1. Backup database
php artisan backup:run

# 2. Run migrations
php artisan migrate

# 3. Verify
php artisan tinker
> Schema::hasTable('product_variants')
> Schema::hasTable('variant_attributes')
> Schema::hasTable('inventory_items')

# 4. Start restructuring create form
# Open: resources/views/admin/products/create.blade.php
# Focus on lines 193-203 (Variants Engine section)
```

---

## 📁 Files Summary

### Created Files ✅

- [x] PRODUCT_SYSTEM_ANALYSIS.md - Full analysis
- [x] 6 migration files for variant system
- [x] 3 model files (ProductVariant, VariantAttribute, InventoryItem)
- [x] 1 service file (VariantGeneratorService)
- [x] This action plan

### Files to Modify ⏳

- [ ] app/Models/Product.php - Add variant relationships
- [ ] app/Http/Controllers/Admin/Product/ProductController.php - Handle variant creation
- [ ] resources/views/admin/products/create.blade.php - Rebuild variant section
- [ ] routes/admin.php - Add variant attribute API route

### Files to Create ⏳

- [ ] app/Http/Controllers/Admin/PurchaseBatchController.php
- [ ] app/Services/FIFOStockService.php
- [ ] resources/views/admin/inventory/batches/index.blade.php
- [ ] resources/views/admin/inventory/batches/create.blade.php

---

**Status**: 🟢 Ready to proceed with migrations
**Next Action**: Run `php artisan migrate` and update Product model
**Estimated Time to MVP**: 12-15 hours
**Blocking Issues**: None - all dependencies resolved
