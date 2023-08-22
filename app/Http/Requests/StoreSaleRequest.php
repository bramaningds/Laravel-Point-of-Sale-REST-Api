<?php

namespace App\Http\Requests;

use Throwable;

use Illuminate\Foundation\Http\FormRequest;

use App\Exceptions\ProductsNotFoundException;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Rules\Sellable;
use App\Rules\SufficientStock;

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
            'customer_id' => 'required_without:customer|numeric|integer',
            'customer.name' => 'required_without:customer_id|string',
            'customer.phone' => 'string',
            'customer.email' => 'string',
            'customer.address' => 'string',
            'items' => 'required',
            'items.*' => [new Sellable, new SufficientStock],
        ];
    }

}
