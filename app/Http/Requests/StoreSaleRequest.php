<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'user_id' => 'required|numeric|integer|exists:users,id',
            'customer_id' => 'required_without:customer|numeric|integer|exists:customers,id',
            'customer.name' => 'required_without:customer_id|string',
            'customer.phone' => 'string',
            'customer.email' => 'string',
            'customer.address' => 'string',
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
                    if ($product->isNotSellable()) {
                        $validator->errors()->add("items.{$index}.id", "The product is not sellable.");
                    }
                }
            },
            function (Validator $validator) use ($products) {
                foreach ($products as $index => $product) {
                    if ($product->hasInsufficientStock($this->input("items.{$index}.quantity"))) {
                        $this->validator->errors()->add(
                            "items.{$index}.quantity", "The product's stock ({$product->stock}) is not sufficient with the required ({$this->input("items.{$index}.quantity")})."
                        );
                    }
                }
            },
        ];
    }
}
