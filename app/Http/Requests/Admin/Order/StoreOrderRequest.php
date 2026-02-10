<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOrderRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'string', 'in:pending,processing,completed,cancelled,refunded'],
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_status' => ['required', 'string', 'in:pending,paid,failed,refunded'],

            // Shipping details
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_email' => ['required', 'email', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['nullable', 'string', 'max:100'],
            'shipping_zip' => ['required', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'max:100'],

            // Order items
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],

            // Optional fields
            'notes' => ['nullable', 'string'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
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
            'user_id' => 'customer',
            'shipping_name' => 'name',
            'shipping_email' => 'email',
            'shipping_phone' => 'phone',
            'shipping_address' => 'address',
            'shipping_city' => 'city',
            'shipping_state' => 'state',
            'shipping_zip' => 'zip code',
            'shipping_country' => 'country',
            'items.*.product_id' => 'product',
            'items.*.quantity' => 'quantity',
            'items.*.price' => 'price',
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
            'user_id.required' => 'Please select a customer',
            'user_id.exists' => 'Selected customer does not exist',
            'status.required' => 'Order status is required',
            'status.in' => 'Invalid order status',
            'payment_method.required' => 'Payment method is required',
            'payment_status.required' => 'Payment status is required',
            'payment_status.in' => 'Invalid payment status',
            'items.required' => 'Order must have at least one item',
            'items.min' => 'Order must have at least one item',
            'items.*.product_id.required' => 'Product is required',
            'items.*.product_id.exists' => 'Selected product does not exist',
            'items.*.quantity.required' => 'Quantity is required',
            'items.*.quantity.min' => 'Quantity must be at least 1',
            'items.*.price.required' => 'Price is required',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (!$this->has('status')) {
            $this->merge(['status' => 'pending']);
        }

        if (!$this->has('payment_status')) {
            $this->merge(['payment_status' => 'pending']);
        }

        if (!$this->has('discount_amount')) {
            $this->merge(['discount_amount' => 0]);
        }

        if (!$this->has('shipping_amount')) {
            $this->merge(['shipping_amount' => 0]);
        }
    }
}
