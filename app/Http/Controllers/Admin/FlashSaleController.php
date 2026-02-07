<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlashSaleController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = FlashSale::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        switch ($status) {
            case 'active':
                $query->active();
                break;
            case 'scheduled':
                $query->scheduled();
                break;
            case 'ended':
                $query->ended();
                break;
        }

        $flashSales = $query->orderBy('starts_at', 'desc')->paginate(10);

        // Stats
        $stats = [
            'total' => FlashSale::count(),
            'active' => FlashSale::active()->count(),
            'scheduled' => FlashSale::scheduled()->count(),
            'ended' => FlashSale::ended()->count(),
        ];

        return view('admin.flash-sales.index', compact('flashSales', 'stats', 'status', 'search'));
    }

    public function create()
    {
        $products = Product::select('id', 'name', 'price', 'stock_quantity')->get();
        return view('admin.flash-sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:1|max:99',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        try {
            DB::beginTransaction();

            $flashSale = FlashSale::create([
                'name' => $request->name,
                'description' => $request->description,
                'discount_percentage' => $request->discount_percentage,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'status' => $request->starts_at <= now() ? 'active' : 'scheduled',
            ]);

            if ($request->has('products')) {
                $products = Product::whereIn('id', $request->products)->get();
                foreach ($products as $product) {
                    $flashSale->products()->attach($product->id, [
                        'discounted_quantity' => min($product->stock_quantity, $request->discounted_quantity ?? $product->stock_quantity),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.flash-sales.index')
                ->with('success', 'Flash sale created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FlashSale creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create flash sale. Please try again.');
        }
    }

    public function show(FlashSale $flashSale)
    {
        $flashSale->load('products');
        return view('admin.flash-sales.show', compact('flashSale'));
    }

    public function edit(FlashSale $flashSale)
    {
        $flashSale->load('products');
        $products = Product::select('id', 'name', 'price', 'stock_quantity')->get();
        return view('admin.flash-sales.edit', compact('flashSale', 'products'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:1|max:99',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        try {
            DB::beginTransaction();

            $flashSale->update([
                'name' => $request->name,
                'description' => $request->description,
                'discount_percentage' => $request->discount_percentage,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'status' => $this->calculateStatus($request->starts_at, $request->ends_at),
            ]);

            if ($request->has('products')) {
                $products = Product::whereIn('id', $request->products)->get();
                $syncData = [];
                foreach ($products as $product) {
                    $syncData[$product->id] = [
                        'discounted_quantity' => min($product->stock_quantity, $request->discounted_quantity ?? $product->stock_quantity),
                    ];
                }
                $flashSale->products()->sync($syncData);
            } else {
                $flashSale->products()->detach();
            }

            DB::commit();

            return redirect()->route('admin.flash-sales.index')
                ->with('success', 'Flash sale updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('FlashSale update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update flash sale. Please try again.');
        }
    }

    public function destroy(FlashSale $flashSale)
    {
        try {
            $flashSale->products()->detach();
            $flashSale->delete();

            return redirect()->route('admin.flash-sales.index')
                ->with('success', 'Flash sale deleted successfully!');
        } catch (\Exception $e) {
            Log::error('FlashSale deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete flash sale. Please try again.');
        }
    }

    public function toggleStatus(FlashSale $flashSale)
    {
        try {
            $newStatus = $flashSale->status === 'active' ? 'ended' : 'active';
            $flashSale->update(['status' => $newStatus]);

            return back()->with('success', 'Flash sale status updated!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status.');
        }
    }

    public function manageProducts(FlashSale $flashSale)
    {
        $flashSale->load('products');
        $products = Product::select('id', 'name', 'price', 'stock_quantity')->get();
        return view('admin.flash-sales.manage-products', compact('flashSale', 'products'));
    }

    public function updateProducts(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        try {
            if ($request->has('products')) {
                $syncData = [];
                foreach ($request->products as $productId) {
                    $product = Product::find($productId);
                    $syncData[$productId] = [
                        'discounted_quantity' => min($product->stock_quantity, $request->discounted_quantity ?? $product->stock_quantity),
                    ];
                }
                $flashSale->products()->sync($syncData);
            } else {
                $flashSale->products()->detach();
            }

            return redirect()->route('admin.flash-sales.show', $flashSale)
                ->with('success', 'Products updated successfully!');
        } catch (\Exception $e) {
            Log::error('FlashSale products update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update products.');
        }
    }

    protected function calculateStatus($startsAt, $endsAt)
    {
        $now = now();
        if ($now->lt($startsAt)) {
            return 'scheduled';
        } elseif ($now->between($startsAt, $endsAt)) {
            return 'active';
        } else {
            return 'ended';
        }
    }
}
