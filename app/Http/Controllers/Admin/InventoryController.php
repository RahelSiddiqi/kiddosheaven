<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseBatch;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $stockFilter = $request->get('filter', 'all');

        $query = Product::with('variants');

        if ($stockFilter === 'low') {
            $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
        } elseif ($stockFilter === 'out') {
            $query->where('stock_quantity', 0);
        }

        $products = $query->withCount('variants')->latest()->paginate(20);

        return view('admin.inventory.index', compact('products', 'stockFilter'));
    }

    public function alerts()
    {
        $lowStockProducts = Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->limit(10)->get();
        $outOfStockProducts = Product::where('stock_quantity', 0)->get();

        // Combine both into a single alerts collection
        $alerts = $lowStockProducts->merge($outOfStockProducts);

        return view('admin.inventory.alerts', compact('alerts'));
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'action' => ['nullable', 'in:add,set,deduct'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $action = $request->action ?? 'set';
        $unitCost = $request->unit_cost ?? ($product->cost_price ?? 0);

        $oldQuantity = $product->stock_quantity;

        switch ($action) {
            case 'add':
                $newQuantity = $oldQuantity + $request->quantity;
                $movementQty = $request->quantity;   // positive = inbound
                break;
            case 'deduct':
                $newQuantity = max(0, $oldQuantity - $request->quantity);
                $movementQty = -1 * ($oldQuantity - $newQuantity); // negative = outbound
                break;
            default: // 'set'
                $newQuantity = $request->quantity;
                $movementQty = $newQuantity - $oldQuantity;
                break;
        }

        DB::transaction(function () use ($product, $newQuantity, $movementQty, $unitCost) {
            $product->update(['stock_quantity' => $newQuantity]);

            // Log the adjustment so the audit trail is complete
            if ($movementQty !== 0) {
                InventoryMovement::create([
                    'product_id'     => $product->id,
                    'movement_type'  => InventoryMovement::TYPE_ADJUSTMENT,
                    'quantity'       => $movementQty,
                    'unit_cost'      => $movementQty > 0 ? $unitCost : null,
                    'notes'          => 'Manual stock adjustment via admin panel',
                    'user_id'        => auth()->check() ? auth()->id() : null,
                ]);
            }

            // ── IMPORTANT: Update or create purchase batch for proper order processing ──
            // If no batches exist, create one from the current stock
            if ($movementQty !== 0) {
                $existingBatch = $product->purchaseBatches()
                    ->where('status', '!=', PurchaseBatch::STATUS_SOLD)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($movementQty > 0) {
                    // Stock being added - create or update batch
                    if ($existingBatch && $existingBatch->status === PurchaseBatch::STATUS_ACTIVE) {
                        // Update existing active batch
                        $existingBatch->increment('remaining_quantity', $movementQty);
                    } else {
                        // Create new batch for this stock addition
                        PurchaseBatch::create([
                            'batch_number'       => 'ADM-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3))),
                            'product_id'         => $product->id,
                            'quantity_received'  => $movementQty,
                            'remaining_quantity' => $movementQty,
                            'quantity_reserved'  => 0,
                            'unit_cost'          => (float) $unitCost,
                            'status'             => PurchaseBatch::STATUS_ACTIVE,
                            'purchase_date'      => now()->toDateString(),
                            'notes'              => 'Admin stock adjustment',
                        ]);
                    }
                } elseif ($movementQty < 0 && $existingBatch) {
                    // Stock being deducted - update batch remaining quantity
                    $existingBatch->decrement('remaining_quantity', abs($movementQty));

                    // Update batch status based on remaining quantity
                    if ($existingBatch->remaining_quantity <= 0) {
                        $existingBatch->update(['status' => PurchaseBatch::STATUS_SOLD]);
                    } elseif ($existingBatch->remaining_quantity < $existingBatch->quantity_received) {
                        $existingBatch->update(['status' => PurchaseBatch::STATUS_PARTIALLY_SOLD]);
                    }
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Stock updated successfully']);
    }
}
