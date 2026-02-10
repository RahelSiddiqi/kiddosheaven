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
        return Auth::check() && Auth::user()->is_admin;
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($productId)
            ],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'catalog_id' => ['required', 'exists:catalogs,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
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
            'catalog_id' => 'catalog',
            'brand_id' => 'brand',
            'sku' => 'SKU',
            'stock_quantity' => 'stock quantity',
            'low_stock_threshold' => 'low stock threshold',
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
            'catalog_id.required' => 'Please select a catalog',
            'catalog_id.exists' => 'Selected catalog does not exist',
            'images.*.image' => 'Each file must be an image',
            'images.*.mimes' => 'Images must be in JPEG, PNG, JPG, GIF, or WEBP format',
            'images.*.max' => 'Each image must not exceed 2MB',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox values to boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active'),
            ]);
        }

        if ($this->has('is_featured')) {
            $this->merge([
                'is_featured' => $this->boolean('is_featured'),
            ]);
        }
    }
}
