<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    public function rules(): array
    {
        $brandId = $this->route('brand')->id ?? null;
        $isUpdate = !empty($brandId);

        return [
            'name' => ['required', 'string', 'max:255', 'unique:brands,name,' . $brandId],
            'slug' => ['required', 'string', 'max:255', 'unique:brands,slug,' . $brandId],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:2048'],
            'website' => ['nullable', 'url', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Brand name is required',
            'logo.required' => 'Brand logo is required',
            'logo.image' => 'The file must be an image',
            'logo.mimes' => 'The image must be a JPEG, PNG, JPG, GIF, or SVG file',
            'logo.max' => 'The image size must not exceed 2MB',
            'website.url' => 'Please enter a valid website URL',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->ajax() || $this->wantsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->toArray()
                ], 422)
            );
        }

        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
