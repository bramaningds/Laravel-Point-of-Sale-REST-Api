<?php

namespace App\Exceptions;

use Exception;

use App\Models\Product;

class ProductHasInsufficientStock extends Exception
{

    public $product;
    public $required;

    public function __construct(Product $product, $required)
    {
        $this->message = "The product #{$product->id} '{$product->name}' has insufficient stock, required={$required}, existing={$product->stock}.";
    }

}
