<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'user_id' => 'required|numeric|integer|exists:users,id',
            'supplier_id' => 'required_without:supplier|numeric|integer|exists:suppliers,id',
            'supplier.name' => 'required_without:supplier_id|string',
            'supplier.phone' => 'string',
            'supplier.email' => 'string',
            'supplier.address' => 'string',
            'discount' => 'numeric',
            'promo' => 'numeric',
            'tax' => 'numeric',
            'items' => 'required',
            'items.*.id' => 'required|numeric|exists:products',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'numeric',
        ];
    }

    protected function passedValidation(): void
    {
        $this->mergeIfMissing([
            'discount' => 0, 'promo' => 0, 'tax' => 0,
        ]);
    }

    public function after(): array
    {
        $products = Product::whereIn('id', $this->input('items.*.id', []))->get();

        return [
            function (Validator $validator) use ($products) {
                foreach ($products as $index => $product) {
                    if ($product->isNotPurchasable()) {
                        $validator->errors()->add("items.{$index}.id", "The product is not purchasable.");
                    }
                }
            },
        ];
    }
}
