<?php

namespace App\Http\Requests;

use Throwable;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
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
            'supplier_id' => 'required_without:supplier|numeric|integer',
            'supplier.name' => 'required_without:supplier_id|string',
            'supplier.phone' => 'string',
            'supplier.email' => 'string',
            'supplier.address' => 'string',
            'items' => 'required',
            'items.*.id' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'numeric',
        ];
    }

}
