<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'numeric|integer',
            'supplier_id' => 'numeric|integer',
            'supplier.name' => 'string',
            'supplier.phone' => 'string',
            'supplier.email' => 'string',
            'supplier.address' => 'string',
            'items.*.product_id' => 'numeric|integer',
            'items.*.quantity' => 'numeric',
            'items.*.price' => 'numeric',
        ];
    }
}
