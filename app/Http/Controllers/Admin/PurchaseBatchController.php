<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseBatch;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseBatchController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }
    public function index(Request $request)
    {
        $query = PurchaseBatch::with('product');

        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('status') && $request->status) {
            if ($request->status == 'active') {
                $query->where('remaining_quantity', '>', 0);
            } elseif ($request->status == 'exhausted') {
                $query->where('remaining_quantity', 0);
            }
        }

        $batches = $query->orderBy('created_at', 'desc')->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('admin.purchase-batches.index', compact('batches', 'products'));
    }

    public function show(PurchaseBatch $purchaseBatch)
    {
        $purchaseBatch->load(['product', 'variant', 'partner', 'movements.product']);

        // Calculate stats
        $quantitySold = $purchaseBatch->quantity_received - $purchaseBatch->remaining_quantity;
        $soldValue = $quantitySold * $purchaseBatch->unit_cost;
        $remainingValue = $purchaseBatch->remaining_quantity * $purchaseBatch->unit_cost;

        return view('admin.purchase-batches.show', compact('purchaseBatch', 'quantitySold', 'soldValue', 'remainingValue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'batch_number' => 'nullable|string|unique:purchase_batches,batch_number',
            'unit_cost' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Ensure selected variant belongs to the same product
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
            $product = Product::findOrFail($validated['product_id']);

            // Allow auto-generated batch numbers if left blank
            $batchNumber = $validated['batch_number'] ?? null;

            // Let InventoryService handle batch creation, movement logging, and counter sync
            $batch = $this->inventoryService->addStock(
                product:  $product,
                quantity: $validated['quantity'],
                unitCost: $validated['unit_cost'],
                details:  [
                    'batch_number' => $batchNumber,
                    'product_variant_id' => $validated['product_variant_id'] ?? null,
                    'supplier'     => $validated['supplier'] ?? null,
                    'expiry_date'  => $validated['expiry_date'] ?? null,
                    'notes'        => $validated['notes'] ?? null,
                ],
            );

            DB::commit();
            return redirect()->route('admin.purchase-batches.index')
                ->with('success', 'Purchase batch created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating purchase batch: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, PurchaseBatch $purchaseBatch)
    {
        $validated = $request->validate([
            'unit_cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $purchaseBatch->update($validated);
            return redirect()->route('admin.purchase-batches.index')
                ->with('success', 'Purchase batch updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating purchase batch: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(PurchaseBatch $purchaseBatch)
    {
        if ($purchaseBatch->remaining_quantity > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete batch with remaining stock.');
        }

        try {
            $purchaseBatch->delete();
            return redirect()->route('admin.purchase-batches.index')
                ->with('success', 'Purchase batch deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting purchase batch: ' . $e->getMessage());
        }
    }

    public function getByProduct(Product $product)
    {
        $batches = $product->purchaseBatches()
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'batches' => $batches,
        ]);
    }
}
