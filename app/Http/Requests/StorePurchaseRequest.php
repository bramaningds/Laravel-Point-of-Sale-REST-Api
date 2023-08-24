<?php

namespace App\Http\Requests;

use Throwable;

use Illuminate\Foundation\Http\FormRequest;

use App\Exceptions\ProductsNotFoundException;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use App\Rules\Purchasable;

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        try {
            // Get all the products
            $products = Product::whereIn('id', $this->input('items.*.id'))->get();

            // Merge the products into the items
            $items = array_map('array_merge', $products->toArray(), $this->input('items'));

            // Replace the items into new items (with products)
            $this->merge(['items' => $items]);

        } catch (Throwable $th) {
            throw new ProductsNotFoundException(array_diff(
                $this->input('items.*.id'),
                $products->pluck('id')->all()
            ));
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $this->merge([
            'items' => array_reduce($this->input('items'), function($items, $item) {
                if (! $items) $items = [];

                $items[$item['id']] = [
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];

                return $items;
            })
        ]);
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
            'items.*' => new Purchasable,
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'numeric',
        ];
    }

}
