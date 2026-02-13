<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
        ]);

        $product = Product::findOrFail($request->product_id);
        $action = $request->action ?? 'set';

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

        DB::transaction(function () use ($product, $newQuantity, $movementQty) {
            $product->update(['stock_quantity' => $newQuantity]);

            // Log the adjustment so the audit trail is complete
            if ($movementQty !== 0) {
                InventoryMovement::create([
                    'product_id'     => $product->id,
                    'movement_type'  => InventoryMovement::TYPE_ADJUSTMENT,
                    'quantity'       => $movementQty,
                    'notes'          => 'Manual stock adjustment via admin panel',
                    'user_id'        => auth()->id(),
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Stock updated successfully']);
    }
}
