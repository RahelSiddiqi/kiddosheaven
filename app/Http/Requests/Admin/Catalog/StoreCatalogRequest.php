<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCatalogRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:category,collection,brand,tag'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:catalogs,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'show_on_homepage' => ['boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'attributes' => ['nullable', 'array'],
            'attributes.*' => ['exists:attributes,id'],
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
            'parent_id' => 'parent catalog',
            'is_active' => 'active status',
            'show_on_homepage' => 'show on homepage',
            'display_order' => 'display order',
            'attributes.*' => 'attribute',
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
            'name.required' => 'Catalog name is required',
            'type.required' => 'Catalog type is required',
            'type.in' => 'Invalid catalog type',
            'parent_id.exists' => 'Selected parent catalog does not exist',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be in JPEG, PNG, JPG, GIF, or WEBP format',
            'image.max' => 'Image must not exceed 2MB',
            'attributes.*.exists' => 'One or more selected attributes do not exist',
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

        if ($this->has('show_on_homepage')) {
            $this->merge([
                'show_on_homepage' => $this->boolean('show_on_homepage'),
            ]);
        }

        // Set default type if not provided
        if (!$this->has('type')) {
            $this->merge(['type' => 'category']);
        }
    }
}
