<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryMovement::with(['product', 'user']);

        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('movement_type', $request->type);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('admin.inventory-movements.index', compact('movements', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'movement_type' => 'required|in:purchase,sale,adjustment,return,transfer',
            'quantity' => 'required|integer|min:1',
            'batch_id' => 'nullable|exists:purchase_batches,id',
            'reference_type' => 'nullable|string|max:100',
            'reference_id' => 'nullable|integer',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Ensure variant belongs to product if provided
        if (!empty($validated['product_variant_id'])) {
            $variantProductId = \App\Models\ProductVariant::where('id', $validated['product_variant_id'])->value('product_id');
            if ((int) $variantProductId !== (int) $validated['product_id']) {
                return redirect()->back()
                    ->with('error', 'Selected variant does not belong to the chosen product.')
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Determine signed quantity: outbound types store negative
            $outboundTypes = ['sale', 'damage', 'transfer'];
            $signedQty = in_array($validated['movement_type'], $outboundTypes)
                ? -1 * abs($validated['quantity'])
                : abs($validated['quantity']);

            // If a batch is specified, update its remaining_quantity
            if (!empty($validated['batch_id'])) {
                $batch = \App\Models\PurchaseBatch::lockForUpdate()->findOrFail($validated['batch_id']);
                if ($signedQty < 0) {
                    // Deducting from batch
                    if ($batch->remaining_quantity < abs($signedQty)) {
                        throw new \Exception('Insufficient batch quantity. Remaining: ' . $batch->remaining_quantity);
                    }
                    $batch->decrement('remaining_quantity', abs($signedQty));
                } else {
                    // Adding to batch (return / purchase)
                    $batch->increment('remaining_quantity', $signedQty);
                }
            }

            $movement = InventoryMovement::create([
                'product_id' => $validated['product_id'],
                'product_variant_id' => $validated['product_variant_id'] ?? null,
                'movement_type' => $validated['movement_type'],
                'quantity' => $signedQty,
                'unit_cost' => $validated['unit_cost'] ?? null,
                'batch_id' => $validated['batch_id'] ?? null,
                'reference_type' => $validated['reference_type'] ?? null,
                'reference_id' => $validated['reference_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            // Sync the denormalised product counter
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
            if ($signedQty > 0) {
                $product->increment('stock_quantity', $signedQty);
            } else {
                $product->decrement('stock_quantity', min(abs($signedQty), $product->stock_quantity));
            }

            // Sync variant counter if provided
            if (!empty($validated['product_variant_id'])) {
                $variant = \App\Models\ProductVariant::lockForUpdate()->find($validated['product_variant_id']);
                if ($variant) {
                    if ($signedQty > 0) {
                        $variant->increment('stock_quantity', $signedQty);
                    } else {
                        $variant->decrement('stock_quantity', min(abs($signedQty), $variant->stock_quantity));
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.inventory-movements.index')
                ->with('success', 'Inventory movement recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error recording inventory movement: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getByProduct(Product $product)
    {
        $movements = $product->inventoryMovements()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'movements' => $movements,
        ]);
    }
}
