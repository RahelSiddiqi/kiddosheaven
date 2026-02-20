<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;

#[Layout('admin.layouts.app')]
#[Title('Purchase Batches - Admin')]
class PurchaseBatchIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    // Modal state
    public bool $showModal = false;

    #[Locked]
    public ?int $editingId = null;

    // Form fields
    public ?int $product_id = null;
    public ?int $product_variant_id = null;
    public ?string $batch_number = null;
    public ?string $unit_cost = null;
    public int $quantity = 1;
    public ?string $supplier = null;
    public ?string $supplier_invoice_number = null;
    public ?string $purchase_date = null;
    public ?string $expiry_date = null;
    public ?string $notes = null;

    public function mount(): void
    {
        $this->purchase_date = now()->toDateString();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    private function batchClass(): string
    {
        return class_exists(\App\Domains\Inventory\Models\PurchaseBatch::class)
            ? \App\Domains\Inventory\Models\PurchaseBatch::class
            : \App\Models\PurchaseBatch::class;
    }

    private function productClass(): string
    {
        return class_exists(\App\Domains\Product\Models\Product::class)
            ? \App\Domains\Product\Models\Product::class
            : \App\Models\Product::class;
    }

    private function variantClass(): string
    {
        return class_exists(\App\Domains\Product\Models\ProductVariant::class)
            ? \App\Domains\Product\Models\ProductVariant::class
            : \App\Models\ProductVariant::class;
    }

    // ── Stats ─────────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        $batchClass = $this->batchClass();

        return [
            'total' => $batchClass::count(),
            'active' => $batchClass::where('remaining_quantity', '>', 0)->count(),
            'total_investment' => $batchClass::sum(DB::raw('quantity_received * unit_cost')),
            'remaining_value' => $batchClass::where('remaining_quantity', '>', 0)
                ->sum(DB::raw('remaining_quantity * unit_cost')),
        ];
    }

    // ── Modal control ─────────────────────────────────────────────

    public function createModal(): void
    {
        $this->reset([
            'product_id', 'product_variant_id', 'batch_number', 'unit_cost',
            'quantity', 'supplier', 'supplier_invoice_number', 'purchase_date',
            'expiry_date', 'notes'
        ]);
        $this->quantity = 1;
        $this->purchase_date = now()->toDateString();
        $this->editingId = null;
        $this->showModal = true;
        $this->resetValidation();
    }

    public function editModal(int $id): void
    {
        $modelClass = $this->batchClass();
        $batch = $modelClass::findOrFail($id);

        $this->editingId = $id;
        $this->product_id = $batch->product_id;
        $this->product_variant_id = $batch->product_variant_id;
        $this->batch_number = $batch->batch_number;
        $this->unit_cost = $batch->unit_cost;
        $this->quantity = $batch->quantity_received;
        $this->supplier = $batch->supplier;
        $this->supplier_invoice_number = $batch->supplier_invoice_number;
        $this->purchase_date = $batch->purchase_date?->format('Y-m-d');
        $this->expiry_date = $batch->expiry_date?->format('Y-m-d');
        $this->notes = $batch->notes;
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
        $modelClass = $this->batchClass();
        $productClass = $this->productClass();
        $variantClass = $this->variantClass();

        $rules = [
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'unit_cost' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'nullable|string|max:255',
            'supplier_invoice_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:purchase_date',
            'notes' => 'nullable|string|max:1000',
        ];

        if ($this->editingId) {
            $rules['batch_number'] = 'nullable|string|unique:purchase_batches,batch_number,' . $this->editingId;
        } else {
            $rules['batch_number'] = 'nullable|string|unique:purchase_batches,batch_number';
        }

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
            if ($this->editingId) {
                // Update existing batch (only metadata fields, not quantities)
                $batch = $modelClass::findOrFail($this->editingId);
                $batch->update([
                    'unit_cost' => $this->unit_cost,
                    'supplier' => $this->supplier ?: null,
                    'supplier_invoice_number' => $this->supplier_invoice_number ?: null,
                    'purchase_date' => $this->purchase_date ?: null,
                    'expiry_date' => $this->expiry_date ?: null,
                    'notes' => $this->notes ?: null,
                ]);

                $this->dispatch('notify', message: 'Purchase batch updated successfully.', type: 'success');
            } else {
                // Create new batch using InventoryService
                $product = $productClass::findOrFail($this->product_id);

                $inventoryService = app(InventoryService::class);
                $inventoryService->addStock(
                    product: $product,
                    quantity: $this->quantity,
                    unitCost: (float) $this->unit_cost,
                    details: [
                        'batch_number' => $this->batch_number ?: null,
                        'product_variant_id' => $this->product_variant_id ?: null,
                        'supplier' => $this->supplier ?: null,
                        'supplier_invoice_number' => $this->supplier_invoice_number ?: null,
                        'purchase_date' => $this->purchase_date ?: now()->toDateString(),
                        'expiry_date' => $this->expiry_date ?: null,
                        'notes' => $this->notes ?: null,
                        'user_id' => auth()->id(),
                    ],
                );

                $this->dispatch('notify', message: 'Purchase batch created successfully.', type: 'success');
            }

            DB::commit();
            $this->closeModal();
            $this->resetPage();
            unset($this->stats);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function markExhausted(int $id): void
    {
        $modelClass = $this->batchClass();

        try {
            $batch = $modelClass::findOrFail($id);
            $batch->update([
                'remaining_quantity' => 0,
                'status' => 'sold',
            ]);

            $this->dispatch('notify', message: 'Batch marked as exhausted.', type: 'success');
            unset($this->stats);
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }

    public function deleteRecord(int $id): void
    {
        $modelClass = $this->batchClass();

        try {
            $batch = $modelClass::findOrFail($id);

            if ($batch->remaining_quantity > 0) {
                $this->dispatch('notify', message: 'Cannot delete batch with remaining stock.', type: 'error');
                return;
            }

            $batch->delete();

            $this->dispatch('notify', message: 'Purchase batch deleted successfully.', type: 'success');
            unset($this->stats);
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error deleting batch: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $modelClass = $this->batchClass();
        $productClass = $this->productClass();

        $batches = $modelClass::with(['product'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('batch_number', 'like', "%{$this->search}%")
                        ->orWhere('supplier', 'like', "%{$this->search}%")
                        ->orWhereHas('product', function ($pq) {
                            $pq->where('name', 'like', "%{$this->search}%")
                               ->orWhere('sku', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->statusFilter === 'active', fn($q) => $q->where('remaining_quantity', '>', 0))
            ->when($this->statusFilter === 'exhausted', fn($q) => $q->where('remaining_quantity', '<=', 0))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $products = $productClass::orderBy('name')->get(['id', 'name', 'sku']);

        return view('livewire.admin.inventory.purchase-batch-index', compact('batches', 'products'));
    }
}
