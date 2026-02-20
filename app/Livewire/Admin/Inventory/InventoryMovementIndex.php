<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Inventory Movements - Admin')]
class InventoryMovementIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $typeFilter = '';

    // Modal state
    public bool $showModal = false;

    #[Locked]
    public ?int $editingId = null;

    // Form fields
    public ?int $product_id = null;
    public ?int $product_variant_id = null;
    public string $movement_type = 'purchase';
    public int $quantity = 1;
    public ?string $unit_cost = null;
    public ?int $batch_id = null;
    public ?string $reference_type = null;
    public ?int $reference_id = null;
    public ?string $notes = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    private function movementClass(): string
    {
        return class_exists(\App\Domains\Inventory\Models\InventoryMovement::class)
            ? \App\Domains\Inventory\Models\InventoryMovement::class
            : \App\Models\InventoryMovement::class;
    }

    private function productClass(): string
    {
        return class_exists(\App\Domains\Product\Models\Product::class)
            ? \App\Domains\Product\Models\Product::class
            : \App\Models\Product::class;
    }

    private function batchClass(): string
    {
        return class_exists(\App\Domains\Inventory\Models\PurchaseBatch::class)
            ? \App\Domains\Inventory\Models\PurchaseBatch::class
            : \App\Models\PurchaseBatch::class;
    }

    private function variantClass(): string
    {
        return class_exists(\App\Domains\Product\Models\ProductVariant::class)
            ? \App\Domains\Product\Models\ProductVariant::class
            : \App\Models\ProductVariant::class;
    }

    // ── Modal control ─────────────────────────────────────────────

    public function createModal(): void
    {
        $this->reset([
            'product_id', 'product_variant_id', 'movement_type', 'quantity',
            'unit_cost', 'batch_id', 'reference_type', 'reference_id', 'notes'
        ]);
        $this->movement_type = 'purchase';
        $this->quantity = 1;
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $modelClass = $this->movementClass();
        $movement = $modelClass::findOrFail($id);

        $this->editingId = $id;
        $this->product_id = $movement->product_id;
        $this->product_variant_id = $movement->product_variant_id;
        $this->movement_type = $movement->movement_type;
        $this->quantity = abs($movement->quantity);
        $this->unit_cost = $movement->unit_cost;
        $this->batch_id = $movement->batch_id;
        $this->reference_type = $movement->reference_type;
        $this->reference_id = $movement->reference_id;
        $this->notes = $movement->notes;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    // ── CRUD ─────────────────────────────────────────────────────

    public function save(): void
    {
        $modelClass = $this->movementClass();
        $productClass = $this->productClass();
        $batchClass = $this->batchClass();
        $variantClass = $this->variantClass();

        $rules = [
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'movement_type' => 'required|in:purchase,sale,adjustment,return,transfer,damage,expire',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'batch_id' => 'nullable|exists:purchase_batches,id',
            'notes' => 'nullable|string|max:1000',
        ];

        $this->validate($rules);

        // Ensure variant belongs to product if provided
        if ($this->product_variant_id) {
            $variantProductId = $variantClass::where('id', $this->product_variant_id)->value('product_id');
            if ((int) $variantProductId !== (int) $this->product_id) {
                $this->addError('product_variant_id', 'Selected variant does not belong to the chosen product.');
                return;
            }
        }

        DB::beginTransaction();
        try {
            // Determine signed quantity: outbound types store negative
            $outboundTypes = ['sale', 'damage', 'transfer', 'expire'];
            $signedQty = in_array($this->movement_type, $outboundTypes)
                ? -1 * abs($this->quantity)
                : abs($this->quantity);

            // Handle batch quantity updates if applicable
            if ($this->batch_id) {
                $batch = $batchClass::lockForUpdate()->findOrFail($this->batch_id);
                if ($signedQty < 0) {
                    if ($batch->remaining_quantity < abs($signedQty)) {
                        throw new \Exception('Insufficient batch quantity. Remaining: ' . $batch->remaining_quantity);
                    }
                    $batch->decrement('remaining_quantity', abs($signedQty));
                } else {
                    $batch->increment('remaining_quantity', $signedQty);
                }
            }

            $data = [
                'product_id' => $this->product_id,
                'product_variant_id' => $this->product_variant_id ?: null,
                'movement_type' => $this->movement_type,
                'quantity' => $signedQty,
                'unit_cost' => $this->unit_cost ?: null,
                'batch_id' => $this->batch_id ?: null,
                'reference_type' => $this->reference_type ?: null,
                'reference_id' => $this->reference_id ?: null,
                'notes' => $this->notes ?: null,
                'user_id' => auth()->id(),
            ];

            if ($this->editingId) {
                $movement = $modelClass::findOrFail($this->editingId);

                // Reverse the previous stock adjustment before applying new one
                $product = $productClass::lockForUpdate()->findOrFail($this->product_id);
                $oldQty = $movement->quantity;
                $qtyDiff = $signedQty - $oldQty;

                if ($qtyDiff > 0) {
                    $product->increment('stock_quantity', $qtyDiff);
                } elseif ($qtyDiff < 0) {
                    $product->decrement('stock_quantity', min(abs($qtyDiff), $product->stock_quantity));
                }

                $movement->update($data);
                $this->dispatch('notify', message: 'Movement updated successfully.', type: 'success');
            } else {
                $modelClass::create($data);

                // Sync the product counter
                $product = $productClass::lockForUpdate()->findOrFail($this->product_id);
                if ($signedQty > 0) {
                    $product->increment('stock_quantity', $signedQty);
                } else {
                    $product->decrement('stock_quantity', min(abs($signedQty), $product->stock_quantity));
                }

                // Sync variant counter if provided
                if ($this->product_variant_id) {
                    $variant = $variantClass::lockForUpdate()->find($this->product_variant_id);
                    if ($variant) {
                        if ($signedQty > 0) {
                            $variant->increment('stock_quantity', $signedQty);
                        } else {
                            $variant->decrement('stock_quantity', min(abs($signedQty), $variant->stock_quantity));
                        }
                    }
                }

                $this->dispatch('notify', message: 'Movement recorded successfully.', type: 'success');
            }

            DB::commit();
            $this->closeModal();
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function deleteRecord(int $id): void
    {
        $modelClass = $this->movementClass();
        $productClass = $this->productClass();
        $variantClass = $this->variantClass();

        DB::beginTransaction();
        try {
            $movement = $modelClass::findOrFail($id);

            // Reverse the stock effect
            $product = $productClass::lockForUpdate()->find($movement->product_id);
            if ($product) {
                if ($movement->quantity > 0) {
                    $product->decrement('stock_quantity', min($movement->quantity, $product->stock_quantity));
                } else {
                    $product->increment('stock_quantity', abs($movement->quantity));
                }
            }

            // Reverse variant stock if applicable
            if ($movement->product_variant_id) {
                $variant = $variantClass::lockForUpdate()->find($movement->product_variant_id);
                if ($variant) {
                    if ($movement->quantity > 0) {
                        $variant->decrement('stock_quantity', min($movement->quantity, $variant->stock_quantity));
                    } else {
                        $variant->increment('stock_quantity', abs($movement->quantity));
                    }
                }
            }

            $movement->delete();
            DB::commit();

            $this->dispatch('notify', message: 'Movement deleted successfully.', type: 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', message: 'Error deleting movement: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $modelClass = $this->movementClass();
        $productClass = $this->productClass();

        $movements = $modelClass::with(['product', 'user', 'purchaseBatch'])
            ->when($this->search, function ($q) {
                $q->whereHas('product', function ($pq) {
                    $pq->where('name', 'like', "%{$this->search}%")
                       ->orWhere('sku', 'like', "%{$this->search}%");
                })
                ->orWhere('movement_number', 'like', "%{$this->search}%")
                ->orWhere('notes', 'like', "%{$this->search}%");
            })
            ->when($this->typeFilter, fn($q) => $q->where('movement_type', $this->typeFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $products = $productClass::orderBy('name')->get(['id', 'name', 'sku']);

        $movementTypes = [
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'adjustment' => 'Adjustment',
            'return' => 'Return',
            'transfer' => 'Transfer',
            'damage' => 'Damage',
            'expire' => 'Expire',
        ];

        return view('livewire.admin.inventory.inventory-movement-index', compact(
            'movements',
            'products',
            'movementTypes'
        ));
    }
}
