<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePurchaseItemRequest extends FormRequest
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
                if ($product->isNotPurchasable()) {
                    $validator->errors()->add('id', 'The product is not purchasable.');
                }
            },
        ];
    }
}
