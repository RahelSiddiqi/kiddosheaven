<?php

namespace App\Livewire\Admin\Product;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\CategoryService;
use App\Services\Product\ProductService;
use Illuminate\Support\Facades\Storage;

#[Layout('admin.layouts.app')]
#[Title('Product Form - Admin')]
class ProductForm extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?int $productId = null;

    // Tab state (not persisted - just UI)
    public string $activeTab = 'basic';

    // ─── Basic ────────────────────────────────────────────────
    public string  $name             = '';
    public string  $slug             = '';
    public string  $sku              = '';
    public string  $barcode          = '';
    public ?int    $categoryId       = null;
    public ?int    $brandId          = null;
    public string  $productType      = 'simple'; // simple | variable | digital
    public string  $deliveryType     = 'instant'; // instant | schedule | frozen
    public string  $shortDescription = '';
    public string  $description      = '';

    // ─── Pricing ──────────────────────────────────────────────
    public string $price          = '';
    public string $costPrice      = '';
    public string $discountPrice  = '';
    public string $discountType   = 'percentage'; // percentage | fixed
    public string $vatRate        = '0';
    public string $wholesalePrice = '';

    // ─── Inventory ────────────────────────────────────────────
    public string $stockQuantity = '0';
    public string $lowStockAlert = '5';
    public string $stockStatus   = 'in_stock'; // in_stock | out_of_stock | pre_order | backorder

    // ─── Physical ─────────────────────────────────────────────
    public string $weight = '';
    public string $length = '';
    public string $width  = '';
    public string $height = '';

    // ─── Content ──────────────────────────────────────────────
    public string $features         = '';
    public string $careInstructions = '';
    public string $ingredients      = '';
    public string $safetyWarning    = '';

    // ─── Media ────────────────────────────────────────────────
    public array  $existingImages = []; // already-saved paths
    public array  $deleteImages   = []; // paths queued for deletion
    public array  $newImages      = []; // TemporaryUploadedFile[]
    public string $primaryImage   = '';
    public string $videoUrl       = '';

    // ─── SEO / Tags ───────────────────────────────────────────
    public string $metaTitle       = '';
    public string $metaDescription = '';
    public string $tagsInput       = ''; // comma-separated

    // ─── Certifications & Policies ────────────────────────────
    public bool   $halalCertified   = false;
    public bool   $organicCertified = false;
    public string $returnPolicy     = '';
    public string $warranty         = '';
    public string $manufacturer     = '';

    // ─── Status ───────────────────────────────────────────────
    public bool $isActive   = true;
    public bool $isFeatured = false;

    // ─── UI helpers ───────────────────────────────────────────
    public array $categories = [];
    public array $brands     = [];

    public function mount(?int $productId = null): void
    {
        $this->productId  = $productId;
        $this->categories = $this->loadCategories();
        $this->brands     = $this->loadBrands();

        if ($productId) {
            $this->loadProduct($productId);
        }
    }

    // ─── Loaders ──────────────────────────────────────────────

    private function loadCategories(): array
    {
        return app(CategoryService::class)
            ->getHierarchicalList()
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->display_name])
            ->toArray();
    }

    private function loadBrands(): array
    {
        $brandClass = class_exists(\App\Domains\Catalog\Models\Brand::class)
            ? \App\Domains\Catalog\Models\Brand::class
            : \App\Models\Brand::class;

        return $brandClass::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
    }

    private function loadProduct(int $id): void
    {
        $productClass = class_exists(\App\Domains\Product\Models\Product::class)
            ? \App\Domains\Product\Models\Product::class
            : \App\Models\Product::class;

        $product = $productClass::findOrFail($id);

        $this->name             = $product->name;
        $this->slug             = $product->slug ?? '';
        $this->sku              = $product->sku ?? '';
        $this->barcode          = $product->barcode ?? '';
        $this->categoryId       = $product->category_id;
        $this->brandId          = $product->brand_id;
        $this->productType      = $product->product_type ?? 'simple';
        $this->deliveryType     = $product->delivery_type ?? 'instant';
        $this->shortDescription = $product->short_description ?? '';
        $this->description      = $product->description ?? '';

        $this->price          = (string) ($product->price ?? '');
        $this->costPrice      = (string) ($product->cost_price ?? '');
        $this->discountPrice  = (string) ($product->discount_price ?? '');
        $this->discountType   = $product->discount_type ?? 'percentage';
        $this->vatRate        = (string) ($product->vat_rate ?? '0');
        $this->wholesalePrice = (string) ($product->wholesale_price ?? '');

        $this->stockQuantity = (string) ($product->stock_quantity ?? '0');
        $this->lowStockAlert = (string) ($product->low_stock_alert ?? '5');
        $this->stockStatus   = $product->stock_status ?? 'in_stock';

        $this->weight = (string) ($product->weight ?? '');
        $this->length = (string) ($product->length ?? '');
        $this->width  = (string) ($product->width ?? '');
        $this->height = (string) ($product->height ?? '');

        $this->features         = $product->features ?? '';
        $this->careInstructions = $product->care_instructions ?? '';
        $this->ingredients      = $product->ingredients ?? '';
        $this->safetyWarning    = $product->safety_warning ?? '';

        $this->existingImages = is_array($product->images) ? $product->images : [];
        $this->primaryImage   = $product->primary_image ?? '';
        $this->videoUrl       = $product->video_url ?? '';

        $this->metaTitle       = $product->meta_title ?? '';
        $this->metaDescription = $product->meta_description ?? '';
        $this->tagsInput       = is_array($product->tags) ? implode(', ', $product->tags) : '';

        $this->halalCertified   = (bool) ($product->halal_certified ?? false);
        $this->organicCertified = (bool) ($product->organic_certified ?? false);
        $this->returnPolicy     = $product->return_policy ?? '';
        $this->warranty         = $product->warranty ?? '';
        $this->manufacturer     = $product->manufacturer ?? '';

        $this->isActive   = (bool) $product->is_active;
        $this->isFeatured = (bool) $product->is_featured;
    }

    // ─── Reactive ─────────────────────────────────────────────

    public function updatedName(): void
    {
        if (!$this->productId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function setPrimaryImage(string $path): void
    {
        $this->primaryImage = $path;
    }

    public function queueDeleteImage(string $path): void
    {
        if (!in_array($path, $this->deleteImages)) {
            $this->deleteImages[] = $path;
        }
        $this->existingImages = array_values(array_filter($this->existingImages, fn($img) => $img !== $path));
        if ($this->primaryImage === $path) {
            $this->primaryImage = $this->existingImages[0] ?? '';
        }
    }

    public function removeNewImage(int $index): void
    {
        unset($this->newImages[$index]);
        $this->newImages = array_values($this->newImages);
    }

    // ─── Save ─────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate($this->rules());

        $tags = array_values(array_filter(
            array_map('trim', explode(',', $this->tagsInput))
        ));

        $data = [
            'name'              => $this->name,
            'category_id'       => $this->categoryId,
            'brand_id'          => $this->brandId ?: null,
            'sku'               => $this->sku ?: null,
            'barcode'           => $this->barcode ?: null,
            'product_type'      => $this->productType,
            'delivery_type'     => $this->deliveryType,
            'short_description' => $this->shortDescription ?: null,
            'description'       => $this->description ?: null,

            'price'           => (float) $this->price,
            'cost_price'      => $this->costPrice !== '' ? (float) $this->costPrice : null,
            'discount_price'  => $this->discountPrice !== '' ? (float) $this->discountPrice : null,
            'discount_type'   => $this->discountType,
            'vat_rate'        => (float) $this->vatRate,
            'wholesale_price' => $this->wholesalePrice !== '' ? (float) $this->wholesalePrice : null,

            'stock_quantity'  => (int) $this->stockQuantity,
            'low_stock_alert' => (int) $this->lowStockAlert,
            'stock_status'    => $this->stockStatus,

            'weight' => $this->weight !== '' ? (float) $this->weight : null,
            'length' => $this->length !== '' ? (float) $this->length : null,
            'width'  => $this->width  !== '' ? (float) $this->width  : null,
            'height' => $this->height !== '' ? (float) $this->height : null,

            'features'          => $this->features ?: null,
            'care_instructions' => $this->careInstructions ?: null,
            'ingredients'       => $this->ingredients ?: null,
            'safety_warning'    => $this->safetyWarning ?: null,

            'video_url'     => $this->videoUrl ?: null,
            'primary_image' => $this->primaryImage ?: null,

            'meta_title'       => $this->metaTitle ?: null,
            'meta_description' => $this->metaDescription ?: null,
            'tags'             => $tags,

            'halal_certified'   => $this->halalCertified,
            'organic_certified' => $this->organicCertified,
            'return_policy'     => $this->returnPolicy ?: null,
            'warranty'          => $this->warranty ?: null,
            'manufacturer'      => $this->manufacturer ?: null,

            'is_active'   => $this->isActive,
            'is_featured' => $this->isFeatured,
        ];

        // Pass new file uploads as images array (ProductService handles UploadedFile instances)
        if (!empty($this->newImages)) {
            $data['images'] = $this->newImages; // ProductService::handleImageUploads() expects UploadedFile[]
        }

        if ($this->productId) {
            // For update: pass existing images and queued deletions
            $data['existing_images'] = $this->existingImages;
            $data['delete_image']    = $this->deleteImages;

            app(ProductService::class)->update($this->productId, $data);
            $this->dispatch('toast', type: 'success', message: 'Product updated successfully.');
        } else {
            app(ProductService::class)->create($data);
            $this->dispatch('toast', type: 'success', message: 'Product created successfully.');
        }

        $this->redirect(route('admin.products.index'), navigate: true);
    }

    // ─── Validation ───────────────────────────────────────────

    protected function rules(): array
    {
        $skuRule = $this->productId
            ? 'nullable|string|max:100|unique:products,sku,' . $this->productId
            : 'nullable|string|max:100|unique:products,sku';

        return [
            'name'             => 'required|string|max:255',
            'categoryId'       => 'required|exists:categories,id',
            'price'            => 'required|numeric|min:0',
            'stockQuantity'    => 'required|integer|min:0',
            'sku'              => $skuRule,
            'costPrice'        => 'nullable|numeric|min:0',
            'discountPrice'    => 'nullable|numeric|min:0',
            'vatRate'          => 'nullable|numeric|min:0|max:100',
            'wholesalePrice'   => 'nullable|numeric|min:0',
            'lowStockAlert'    => 'nullable|integer|min:0',
            'weight'           => 'nullable|numeric|min:0',
            'length'           => 'nullable|numeric|min:0',
            'width'            => 'nullable|numeric|min:0',
            'height'           => 'nullable|numeric|min:0',
            'metaTitle'        => 'nullable|string|max:255',
            'metaDescription'  => 'nullable|string|max:500',
            'videoUrl'         => 'nullable|url|max:500',
            'newImages'        => 'nullable|array',
            'newImages.*'      => 'image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'productType'      => 'in:simple,variable,digital',
            'deliveryType'     => 'in:instant,schedule,frozen',
            'stockStatus'      => 'in:in_stock,out_of_stock,pre_order,backorder',
            'discountType'     => 'in:percentage,fixed',
        ];
    }

    protected function messages(): array
    {
        return [
            'categoryId.required' => 'Please select a category.',
            'price.required'      => 'Price is required.',
            'price.numeric'       => 'Price must be a valid number.',
            'stockQuantity.required' => 'Stock quantity is required.',
            'sku.unique'          => 'This SKU is already in use.',
            'newImages.*.image'   => 'Each file must be an image.',
            'newImages.*.max'     => 'Each image must not exceed 20 MB.',
        ];
    }

    public function render()
    {
        return view('livewire.admin.product.product-form');
    }
}
