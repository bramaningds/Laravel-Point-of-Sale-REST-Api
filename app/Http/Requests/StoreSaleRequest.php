<?php

namespace App\Http\Requests;

use Throwable;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'user_id' => 'required|numeric|integer',
            'customer_id' => 'required_without:customer|numeric|integer',
            'customer.name' => 'required_without:customer_id|string',
            'customer.phone' => 'string',
            'customer.email' => 'string',
            'customer.address' => 'string',
            'items' => 'required',
            'items.*.id' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'numeric',
        ];
    }

}
