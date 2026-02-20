<?php

namespace App\Http\Requests\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->is_admin || Auth::user()->hasPermission('manage-orders'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', 'in:pending,processing,completed,cancelled,refunded'],
            'payment_status' => ['nullable', 'string', 'in:pending,paid,failed,refunded'],

            // Shipping details
            'shipping_name' => ['nullable', 'string', 'max:255'],
            'shipping_email' => ['nullable', 'email', 'max:255'],
            'shipping_phone' => ['nullable', 'string', 'max:20'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'shipping_city' => ['nullable', 'string', 'max:100'],
            'shipping_state' => ['nullable', 'string', 'max:100'],
            'shipping_zip' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'max:100'],

            // Optional fields
            'notes' => ['nullable', 'string'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'shipped_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
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
            'shipping_name' => 'name',
            'shipping_email' => 'email',
            'shipping_phone' => 'phone',
            'shipping_address' => 'address',
            'shipping_city' => 'city',
            'shipping_state' => 'state',
            'shipping_zip' => 'zip code',
            'shipping_country' => 'country',
            'tracking_number' => 'tracking number',
            'shipped_at' => 'shipped date',
            'delivered_at' => 'delivered date',
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
            'status.in' => 'Invalid order status',
            'payment_status.in' => 'Invalid payment status',
            'shipped_at.date' => 'Shipped date must be a valid date',
            'delivered_at.date' => 'Delivered date must be a valid date',
        ];
    }
}
