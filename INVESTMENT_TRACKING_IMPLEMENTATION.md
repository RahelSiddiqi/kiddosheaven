# Investment & Purchase Tracking Implementation Guide

## 📊 Overview

This enhancement connects the **Investment** system with **Purchase Batches** to provide complete financial tracking from investment → purchase → sales → profit/ROI.

---

## 🗄️ Database Changes

### 1. Purchase Batches Table Enhancement

**New Columns:**
```sql
investment_id        BIGINT UNSIGNED NULL    -- Links to investments table
payment_method       ENUM                     -- How purchase was funded
```

**Payment Methods:**
- `cash` - Paid from cash reserves
- `investment` - Funded by investor capital
- `loan` - Borrowed funds
- `partner_capital` - From partner contributions
- `other` - Other funding sources

**Foreign Key:**
```sql
FOREIGN KEY (investment_id) REFERENCES investments(id) ON DELETE SET NULL
```

### 2. Investments Table Enhancement

**New Columns:**
```sql
spent_amount    DECIMAL(12,2) default 0  -- Amount allocated to purchases
investor_id     BIGINT UNSIGNED NULL     -- Links to investors table
```

**New Indexes:**
```sql
INDEX (investment_date)
INDEX (investor_id, status)
```

---

## 📦 Model Enhancements

### Investment Model

**New Relationships:**
```php
// Get all purchase batches funded by this investment
public function purchaseBatches()
{
    return $this->hasMany(PurchaseBatch::class);
}
```

**New Accessors:**
```php
// Available balance = total investment - spent amount
$investment->available_balance

// Current inventory value from linked batches  
$investment->inventory_value
```

**New Methods:**
```php
// Auto-calculate current value based on:
// 1. Remaining inventory value
// 2. Revenue from sold inventory
$investment->calculateCurrentValue()
```

### PurchaseBatch Model

**New Relationships:**
```php
// Get the investment that funded this batch
public function investment()
{
    return $this->belongsTo(Investment::class);
}
```

**New Accessor:**
```php
// Total cost of this batch (unit_cost × quantity)
$batch->total_cost
```

**New Constants:**
```php
const PAYMENT_CASH = 'cash';
const PAYMENT_INVESTMENT = 'investment';
const PAYMENT_LOAN = 'loan';
const PAYMENT_PARTNER_CAPITAL = 'partner_capital';
const PAYMENT_OTHER = 'other';
```

---

## 🔄 Complete Financial Flow

### Step 1: Receive Investment
```php
Investment::create([
    'investor_id' => 1,
    'title' => 'Inventory Investment Round 1',
    'amount' => 100000,  // ৳100,000
    'spent_amount' => 0,
    'type' => 'inventory',
    'status' => 'active',
    'investment_date' => now(),
]);
```

**Result:** ৳100,000 available for purchases

### Step 2: Purchase Inventory
```php
PurchaseBatch::create([
    'product_id' => 5,
    'investment_id' => 1,           // ← Links to investment
    'payment_method' => 'investment', // ← Tracks funding source
    'unit_cost' => 50,
    'quantity_received' => 1000,
    'remaining_quantity' => 1000,
    'purchase_date' => now(),
]);
```

**Auto-update Investment:**
```php
// spent_amount = ৳50,000 (1000 × 50)
// available_balance = ৳50,000 (100,000 - 50,000)
```

### Step 3: Sell Products
```
Order → OrderItems → Deducts from batch remaining_quantity (FIFO)
```

### Step 4: Calculate ROI
```php
$investment = Investment::with('purchaseBatches')->find(1);

// Inventory still in stock
$inventoryValue = $investment->purchaseBatches
    ->sum(fn($b) => $b->remaining_quantity * $b->unit_cost);
    
// Value converted to sales (approximated with 30% markup)
$revenueValue = $investment->purchaseBatches
    ->sum(function($b) {
        $soldQty = $b->quantity_received - $b->remaining_quantity;
        return $soldQty * $b->unit_cost * 1.3;
    });

$totalValue = $inventoryValue + $revenueValue;
$roi = (($totalValue - $investment->amount) / $investment->amount) * 100;
```

---

## 📈 Enhanced Reports

### Investments Report Enhancement

**New Metrics:**
```php
$totalInvested       // Total capital invested
$totalSpent          // Amount allocated to purchases
$totalAvailable      // Unspent investment capital
$totalInventoryValue // Current value of inventory bought with investments
$roi                 // Return on investment percentage
```

**Query:**
```php
Investment::with(['investor', 'purchaseBatches'])
    ->where('status', 'active')
    ->get();
```

---

## 💡 Usage Examples

### Example 1: Track Investment Utilization
```php
$investment = Investment::find(1);

echo "Total Investment: ৳" . number_format($investment->amount);
echo "Spent on Purchases: ৳" . number_format($investment->spent_amount);
echo "Available Balance: ৳" . number_format($investment->available_balance);
echo "Current Inventory Value: ৳" . number_format($investment->inventory_value);
```

### Example 2: Create Purchase from Investment
```php
// Find investment with available balance
$investment = Investment::where('status', 'active')
    ->whereRaw('amount > spent_amount')
    ->first();

if ($investment && $investment->available_balance >= $purchaseCost) {
    $batch = PurchaseBatch::create([
        'product_id' => $productId,
        'investment_id' => $investment->id,
        'payment_method' => 'investment',
        'unit_cost' => $unitCost,
        'quantity_received' => $quantity,
        'remaining_quantity' => $quantity,
    ]);
    
    // Update investment spent amount
    $investment->increment('spent_amount', $batch->total_cost);
}
```

### Example 3: Investor Performance Report
```php
$investor = Investor::with('investments.purchaseBatches')->find(1);

foreach ($investor->investments as $investment) {
    $inventoryValue = $investment->purchaseBatches
        ->sum(fn($b) => $b->remaining_quantity * $b->unit_cost);
    
    $purchaseCount = $investment->purchaseBatches->count();
    $totalUnits = $investment->purchaseBatches
        ->sum('quantity_received');
    
    echo "Investment: {$investment->title}\n";
    echo "  Batches Purchased: {$purchaseCount}\n";
    echo "  Total Units: {$totalUnits}\n";
    echo "  Current Inventory: ৳{$inventoryValue}\n";
}
```

---

## 🔐 Data Integrity Rules

### Constraints

1. **Investment Balance**
   ```
   spent_amount <= amount
   available_balance = amount - spent_amount
   ```

2. **Purchase Validation**
   ```php
   if ($investment->available_balance < $purchaseCost) {
       throw new InsufficientFundsException();
   }
   ```

3. **Batch-Investment Link**
   - Optional: Purchases can exist without investment_id (cash purchases)
   - When linked: payment_method should be 'investment'

---

## 📊 Reporting Queries

### Total Investment Utilization
```php
$stats = Investment::selectRaw('
    SUM(amount) as total_invested,
    SUM(spent_amount) as total_spent,
    SUM(amount - COALESCE(spent_amount, 0)) as total_available
')->first();
```

### Inventory Purchased Per Investment Type
```php
$breakdown = Investment::with('purchaseBatches')
    ->get()
    ->groupBy('type')
    ->map(function ($investments) {
        return [
            'total_invested' => $investments->sum('amount'),
            'total_batches' => $investments->sum(fn($i) => $i->purchaseBatches->count()),
            'inventory_value' => $investments->sum('inventory_value'),
        ];
    });
```

### ROI by Investor
```php
$investors = Investor::with('investments.purchaseBatches')
    ->get()
    ->map(function ($investor) {
        $totalInvested = $investor->investments->sum('amount');
        $currentValue = $investor->investments->sum(function ($inv) {
            return $inv->calculateCurrentValue();
        });
        
        return [
            'name' => $investor->name,
            'invested' => $totalInvested,
            'current_value' => $currentValue,
            'roi' => (($currentValue - $totalInvested) / $totalInvested) * 100,
        ];
    });
```

---

## ✅ Migration Instructions

### Run Migrations
```bash
php artisan migrate
```

**Tables Updated:**
- `purchase_batches` → Added investment_id, payment_method
- `investments` → Added spent_amount, improved indexes

### Rollback (if needed)
```bash
php artisan migrate:rollback --step=2
```

---

## 🎯 Best Practices

### DO:
✅ Link purchases to investments when using investor funds  
✅ Update `spent_amount` when creating batches from investment  
✅ Set `payment_method` correctly  
✅ Use `available_balance` before allocating funds  
✅ Track ROI using `calculateCurrentValue()`

### DON'T:
❌ Set investment_id without setting payment_method='investment'  
❌ Manually set spent_amount (should be calculated)  
❌ Delete investments with linked batches (cascade will remove batches)  
❌ Record product purchases in expenses table  

---

## 🔄 Backward Compatibility

**Existing Data:**
- All existing `purchase_batches` will have `investment_id = NULL`
- All existing `investments` will have `spent_amount = 0`
- System continues to work without linking purchases to investments
- Optional feature - can be adopted gradually

**No Breaking Changes:**
- All existing relationships intact
- All existing queries work unchanged
- New columns are nullable/have defaults

---

## 📝 Summary

This enhancement provides:

1. **Complete Money Trail**: Investment → Purchase → Sale → Profit
2. **Automatic ROI Calculation**: Based on actual inventory performance
3. **Investment Utilization Tracking**: Know how much is spent vs available
4. **Better Investor Reporting**: Show exactly what their money bought
5. **Flexible Funding**: Support multiple payment methods
6. **Backward Compatible**: Works with existing data

The system now properly tracks the **SOURCE** of funding (investments) and the **USE** of funds (purchase batches), enabling accurate financial reporting and investor relations.
