<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->is_admin || Auth::user()->hasPermission('manage-products'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            // Basic Information
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'barcode' => ['nullable', 'string', 'max:100'],
            'product_type' => ['nullable', 'string', 'in:simple,variable,digital'],
            'delivery_type' => ['nullable', 'string', 'in:instant,schedule,frozen'],

            // Pricing
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'string', 'in:percentage,fixed'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],

            // Inventory
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_alert' => ['nullable', 'integer', 'min:0'],
            'stock_status' => ['nullable', 'string', 'in:in_stock,out_of_stock,pre_order,backorder'],

            // Physical Attributes
            'weight' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],

            // Content
            'features' => ['nullable', 'string'],
            'care_instructions' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'string'],
            'safety_warning' => ['nullable', 'string'],

            // Media
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:20480'],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*' => ['string'],
            'primary_image' => ['nullable', 'string', 'max:500'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'primary_image' => ['nullable', 'string'],
            'delete_image' => ['nullable', 'array'],
            'delete_image.*' => ['string'],

            // Tags
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string', 'max:50'],

            // SEO
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],

            // Status
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'is_active' => ['nullable'],
            'is_featured' => ['nullable'],

            // Certifications & Policies
            'halal_certified' => ['nullable'],
            'organic_certified' => ['nullable'],
            'return_policy' => ['nullable', 'string'],
            'warranty' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],

            // Variants (for variable products)
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock_quantity' => ['nullable', 'integer', 'min:0'],
            'variants.*.barcode' => ['nullable', 'string', 'max:100'],
            'variants.*.is_default' => ['nullable'],
            'variants.*.is_active' => ['nullable'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variants.*.attributes.*' => ['nullable', 'integer'],

            // Non-Variant Attributes
            'non_variant_attributes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'brand_id' => 'brand',
            'sku' => 'SKU',
            'stock_quantity' => 'stock quantity',
            'low_stock_alert' => 'low stock alert',
            'is_active' => 'active status',
            'is_featured' => 'featured status',
            'images.*' => 'image',
            'tags.*' => 'tag',
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'price.required' => 'Product price is required',
            'price.numeric' => 'Price must be a valid number',
            'price.min' => 'Price cannot be negative',
            'stock_quantity.required' => 'Stock quantity is required',
            'stock_quantity.integer' => 'Stock quantity must be a whole number',
            'category_id.required' => 'Please select a category',
            'category_id.exists' => 'Selected category does not exist',
            'images.*.image' => 'Each file must be an image',
            'images.*.mimes' => 'Images must be in JPEG, PNG, JPG, GIF, or WEBP format',
            'images.*.max' => 'Each image must not exceed 20MB',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Derive is_active from status dropdown (status field is used in the form, not a checkbox)
        $isActive = $this->has('is_active')
            ? $this->boolean('is_active')
            : ($this->input('status') === 'active');

        $this->merge([
            'is_active' => $isActive,
            'is_featured' => $this->boolean('is_featured'),
            'halal_certified' => $this->boolean('halal_certified'),
            'organic_certified' => $this->boolean('organic_certified'),
        ]);

        if (!$this->filled('vat_rate')) {
            $this->merge(['vat_rate' => 0]);
        }

        // Filter empty tags
        if ($this->has('tags') && is_array($this->tags)) {
            $this->merge([
                'tags' => array_values(array_filter($this->tags)),
            ]);
        }
    }
}
