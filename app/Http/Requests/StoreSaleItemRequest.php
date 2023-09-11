<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSaleItemRequest extends FormRequest
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
            'id' => 'required|integer|exists:products',
            'quantity' => 'required|numeric',
            'price' => 'numeric',
        ];
    }

    public function after(): array
    {
        $product = Product::find($this->input('id'));

        return [
            function (Validator $validator) use ($product) {
                if ($product->isNotSellable()) {
                    $validator->errors()->add('id', 'The product is not sellable.');
                }
            },
            function (Validator $validator) use ($product) {
                if ($product->hasInsufficientStock($this->input('quantity'))) {
                    $this->validator->errors()->add(
                        'quantity', "The product's stock ({$product->stock}) is not sufficient with the required ({$this->input('quantity')})."
                    );
                }
            },
        ];
    }
}
