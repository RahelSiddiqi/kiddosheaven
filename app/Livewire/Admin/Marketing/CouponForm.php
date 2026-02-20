<?php

namespace App\Livewire\Admin\Marketing;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Illuminate\Support\Str;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;

#[Layout('admin.layouts.app')]
#[Title('Coupon Form - Admin')]
class CouponForm extends Component
{
    #[Locked]
    public ?int $couponId = null;

    // Form fields
    public string  $code                  = '';
    public string  $description           = '';
    public string  $discountType          = 'percentage';
    public float   $discountValue         = 0;
    public float   $minimumOrderAmount   = 0;
    public ?float  $maximumDiscountAmount = null;
    public ?int    $usageLimit            = null;
    public ?int    $usageLimitPerUser     = null;
    public ?string $startDate             = null;
    public ?string $endDate               = null;
    public bool    $isActive              = true;
    public bool    $isUserSpecific        = false;
    public array   $selectedProducts      = [];
    public array   $selectedCategories    = [];

    // Available options
    public array $availableProducts   = [];
    public array $availableCategories = [];

    public function mount(?int $couponId = null): void
    {
        $this->couponId = $couponId;

        $this->availableProducts   = Product::where('is_active', true)->orderBy('name')->get(['id', 'name'])->toArray();
        $this->availableCategories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name'])->toArray();

        if ($couponId) {
            $coupon = Coupon::with(['products', 'categories'])->findOrFail($couponId);
            $this->code                  = $coupon->code;
            $this->description           = $coupon->description ?? '';
            $this->discountType          = $coupon->discount_type;
            $this->discountValue         = (float) $coupon->discount_value;
            $this->minimumOrderAmount   = (float) ($coupon->minimum_order_amount ?? 0);
            $this->maximumDiscountAmount = $coupon->maximum_discount_amount ? (float) $coupon->maximum_discount_amount : null;
            $this->usageLimit            = $coupon->usage_limit;
            $this->usageLimitPerUser     = $coupon->usage_limit_per_user;
            $this->startDate             = $coupon->start_date?->format('Y-m-d');
            $this->endDate               = $coupon->end_date?->format('Y-m-d');
            $this->isActive              = (bool) $coupon->is_active;
            $this->isUserSpecific        = (bool) $coupon->is_user_specific;
            $this->selectedProducts      = $coupon->products->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $this->selectedCategories    = $coupon->categories->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            // Auto-generate code
            $this->code = strtoupper(Str::random(8));
        }
    }

    protected function rules(): array
    {
        $uniqueRule = $this->couponId
            ? 'unique:coupons,code,' . $this->couponId
            : 'unique:coupons,code';

        return [
            'code'                  => "required|string|max:50|{$uniqueRule}",
            'discountType'          => 'required|in:percentage,fixed',
            'discountValue'         => 'required|numeric|min:0',
            'minimumOrderAmount'   => 'nullable|numeric|min:0',
            'maximumDiscountAmount' => 'nullable|numeric|min:0',
            'usageLimit'            => 'nullable|integer|min:1',
            'usageLimitPerUser'     => 'nullable|integer|min:1',
            'startDate'             => 'nullable|date',
            'endDate'               => 'nullable|date|after_or_equal:startDate',
            'isActive'              => 'boolean',
            'selectedProducts'      => 'nullable|array',
            'selectedCategories'    => 'nullable|array',
        ];
    }

    public function generateCode(): void
    {
        $this->code = strtoupper(Str::random(8));
    }

    public function save(): void
    {
        $validated = $this->validate();

        $data = [
            'code'                    => strtoupper($this->code),
            'description'             => $this->description ?: null,
            'discount_type'           => $this->discountType,
            'discount_value'          => $this->discountValue,
            'minimum_order_amount'    => $this->minimumOrderAmount ?: 0,
            'maximum_discount_amount' => $this->maximumDiscountAmount ?: null,
            'usage_limit'             => $this->usageLimit ?: null,
            'usage_limit_per_user'    => $this->usageLimitPerUser ?: null,
            'start_date'              => $this->startDate ?: null,
            'end_date'                => $this->endDate ?: null,
            'is_active'               => $this->isActive,
            'is_user_specific'        => $this->isUserSpecific,
        ];

        if ($this->couponId) {
            $coupon = Coupon::findOrFail($this->couponId);
            $coupon->update($data);
            $coupon->products()->sync($this->selectedProducts);
            $coupon->categories()->sync($this->selectedCategories);
            $this->dispatch('notify', message: 'Coupon updated.', type: 'success');
        } else {
            $coupon = Coupon::create($data);
            if ($this->selectedProducts) $coupon->products()->attach($this->selectedProducts);
            if ($this->selectedCategories) $coupon->categories()->attach($this->selectedCategories);
            $this->dispatch('notify', message: 'Coupon created.', type: 'success');
        }

        $this->redirect(route('admin.coupons.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.marketing.coupon-form');
    }
}
