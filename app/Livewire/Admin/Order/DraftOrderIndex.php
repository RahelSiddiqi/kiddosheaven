<?php

namespace App\Livewire\Admin\Order;

use App\Domains\Order\Models\DraftOrder;
use App\Domains\Product\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Draft Orders')]
class DraftOrderIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public bool   $showCreate  = false;
    public ?int   $viewingId   = null;

    // Create-draft quick form
    public string $customer_name  = '';
    public string $customer_email = '';
    public string $customer_phone = '';
    public string $note           = '';
    public string $payment_method = 'cod';
    public array  $lineItems      = [];    // [{product_id, name, qty, price}]
    public string $productSearch  = '';
    public array  $productResults = [];

    public function updatedSearch(): void { $this->resetPage(); }

    // ── Line item management ───────────────────────────────────────

    public function searchProducts(): void
    {
        if (strlen($this->productSearch) < 2) {
            $this->productResults = [];
            return;
        }
        $this->productResults = Product::where('name', 'like', "%{$this->productSearch}%")
            ->orWhere('sku', 'like', "%{$this->productSearch}%")
            ->select('id', 'name', 'sku', 'price', 'stock_quantity')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function addLineItem(int $productId): void
    {
        $product = Product::findOrFail($productId);

        // If already in list, just increment qty
        foreach ($this->lineItems as $i => $item) {
            if ($item['product_id'] === $productId && empty($item['variant_id'])) {
                $this->lineItems[$i]['qty'] += 1;
                $this->lineItems[$i]['total'] = $this->lineItems[$i]['qty'] * $this->lineItems[$i]['price'];
                $this->productSearch   = '';
                $this->productResults  = [];
                return;
            }
        }

        $this->lineItems[] = [
            'product_id' => $product->id,
            'name'       => $product->name,
            'sku'        => $product->sku ?? '',
            'qty'        => 1,
            'price'      => (float) $product->price,
            'total'      => (float) $product->price,
        ];

        $this->productSearch  = '';
        $this->productResults = [];
    }

    public function removeLineItem(int $index): void
    {
        array_splice($this->lineItems, $index, 1);
    }

    public function updatedLineItems(): void
    {
        foreach ($this->lineItems as $i => $item) {
            $this->lineItems[$i]['total'] = ($item['qty'] ?? 1) * ($item['price'] ?? 0);
        }
    }

    // ── Save draft ─────────────────────────────────────────────────

    public function saveDraft(): void
    {
        $this->validate([
            'customer_name' => 'nullable|string|max:200',
            'customer_email'=> 'nullable|email|max:200',
        ]);

        if (empty($this->lineItems)) {
            $this->dispatch('toast', type: 'error', message: 'Add at least one product.');
            return;
        }

        $subtotal = collect($this->lineItems)->sum('total');

        DraftOrder::create([
            'customer_name'  => $this->customer_name ?: null,
            'customer_email' => $this->customer_email ?: null,
            'customer_phone' => $this->customer_phone ?: null,
            'line_items'     => $this->lineItems,
            'subtotal'       => $subtotal,
            'total_amount'   => $subtotal,
            'payment_method' => $this->payment_method,
            'note'           => $this->note ?: null,
            'status'         => 'open',
        ]);

        $this->reset(['customer_name', 'customer_email', 'customer_phone', 'note', 'lineItems', 'productSearch', 'productResults']);
        $this->payment_method = 'cod';
        $this->showCreate     = false;
        $this->dispatch('toast', type: 'success', message: 'Draft order created.');
    }

    public function cancel(int $id): void
    {
        DraftOrder::findOrFail($id)->update(['status' => 'cancelled']);
    }

    public function subtotal(): float
    {
        return collect($this->lineItems)->sum('total');
    }

    public function render()
    {
        $drafts = DraftOrder::query()
            ->when($this->search, fn($q) => $q
                ->where('draft_number', 'like', "%{$this->search}%")
                ->orWhere('customer_name', 'like', "%{$this->search}%")
                ->orWhere('customer_email', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('livewire.admin.order.draft-order-index', compact('drafts'));
    }
}
