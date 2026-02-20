<?php

namespace App\Livewire\Admin\Marketing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Illuminate\Support\Str;
use App\Models\FlashSale;
use App\Models\Product;

#[Layout('admin.layouts.app')]
#[Title('Flash Sale Form - Admin')]
class FlashSaleForm extends Component
{
    #[Locked]
    public ?int $flashSaleId = null;

    // Form fields
    public string  $title           = '';
    public string  $subtitle        = '';
    public string  $backgroundColor = '#ff6b6b';
    public string  $textColor       = '#ffffff';
    public ?string $startsAt        = null;
    public ?string $endsAt          = null;
    public string  $status          = 'draft';

    // Products with pivot data
    public array $flashProducts = []; // [['id' => ..., 'discount_price' => ..., 'discount_type' => ..., 'limit_per_customer' => ...]]
    public array $allProducts   = [];

    // Product search/add
    public string $productSearch    = '';
    public ?int   $addingProductId  = null;

    public function mount(?int $flashSaleId = null): void
    {
        $this->flashSaleId = $flashSaleId;
        $this->allProducts = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price'])->toArray();

        if ($flashSaleId) {
            $sale = FlashSale::with(['products' => fn($q) => $q->withPivot('discount_price', 'discount_type', 'limit_per_customer')])->findOrFail($flashSaleId);

            $this->title           = $sale->title;
            $this->subtitle        = $sale->subtitle ?? '';
            $this->backgroundColor = $sale->background_color ?? '#ff6b6b';
            $this->textColor       = $sale->text_color ?? '#ffffff';
            $this->startsAt        = $sale->starts_at?->format('Y-m-d\TH:i') ?? $sale->start_at?->format('Y-m-d\TH:i');
            $this->endsAt          = $sale->ends_at?->format('Y-m-d\TH:i') ?? $sale->end_at?->format('Y-m-d\TH:i');
            $this->status          = $sale->status ?? 'draft';

            $this->flashProducts = $sale->products->map(fn($p) => [
                'id'                 => $p->id,
                'name'               => $p->name,
                'price'              => $p->price,
                'discount_price'     => $p->pivot->discount_price,
                'discount_type'      => $p->pivot->discount_type,
                'limit_per_customer' => $p->pivot->limit_per_customer,
            ])->toArray();
        }
    }

    public function getFilteredProductsProperty(): array
    {
        $addedIds = collect($this->flashProducts)->pluck('id')->toArray();

        return collect($this->allProducts)
            ->reject(fn($p) => in_array($p['id'], $addedIds))
            ->when($this->productSearch, fn($c) => $c->filter(fn($p) => str_contains(strtolower($p['name']), strtolower($this->productSearch))))
            ->values()
            ->take(8)
            ->toArray();
    }

    public function addProduct(int $productId): void
    {
        if (collect($this->flashProducts)->contains('id', $productId)) {
            return;
        }

        $product = collect($this->allProducts)->firstWhere('id', $productId);
        if (!$product) return;

        $this->flashProducts[] = [
            'id'                 => $product['id'],
            'name'               => $product['name'],
            'price'              => $product['price'],
            'discount_price'     => round($product['price'] * 0.9, 2), // default 10% off
            'discount_type'      => 'fixed',
            'limit_per_customer' => null,
        ];

        $this->productSearch = '';
    }

    public function removeProduct(int $index): void
    {
        unset($this->flashProducts[$index]);
        $this->flashProducts = array_values($this->flashProducts);
    }

    public function save(): void
    {
        $this->validate([
            'title'           => 'required|string|max:255',
            'backgroundColor' => 'nullable|string|max:20',
            'textColor'       => 'nullable|string|max:20',
            'startsAt'        => 'required|date',
            'endsAt'          => 'required|date|after:startsAt',
            'flashProducts'   => 'required|array|min:1',
            'flashProducts.*.id'             => 'required|integer',
            'flashProducts.*.discount_price' => 'required|numeric|min:0',
            'flashProducts.*.discount_type'  => 'required|in:percentage,fixed',
        ]);

        $data = [
            'title'            => $this->title,
            'slug'             => Str::slug($this->title),
            'subtitle'         => $this->subtitle ?: null,
            'background_color' => $this->backgroundColor,
            'text_color'       => $this->textColor,
            'starts_at'        => $this->startsAt,
            'ends_at'          => $this->endsAt,
            'status'           => $this->status,
        ];

        if ($this->flashSaleId) {
            $sale = FlashSale::findOrFail($this->flashSaleId);
            $sale->update($data);
            $sale->products()->detach();
        } else {
            $sale = FlashSale::create($data);
        }

        foreach ($this->flashProducts as $row) {
            $sale->products()->attach($row['id'], [
                'discount_price'     => $row['discount_price'],
                'discount_type'      => $row['discount_type'],
                'limit_per_customer' => $row['limit_per_customer'] ?: null,
            ]);
        }

        $this->dispatch('notify', message: $this->flashSaleId ? 'Flash sale updated.' : 'Flash sale created.', type: 'success');
        $this->redirect(route('admin.flash-sales.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.marketing.flash-sale-form', [
            'filteredProducts' => $this->filteredProducts,
        ]);
    }
}
