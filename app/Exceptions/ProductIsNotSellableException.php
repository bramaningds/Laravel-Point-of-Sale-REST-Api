<?php

namespace App\Exceptions;

use Exception;

class ProductIsNotSellableException extends Exception
{

    public function __construct(Product $product)
    {
        $this->message = "The product {$product->id} is not sellable.";
    }
}
