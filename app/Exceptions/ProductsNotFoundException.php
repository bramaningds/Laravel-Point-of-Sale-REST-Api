<?php

namespace App\Exceptions;

use Exception;

class ProductsNotFoundException extends Exception
{

    public function __construct($ids = [])
    {
        $ids = collect($ids)->join(', ', ', and ');
        $this->message = "Product(s) with id {$ids} are not found.";
    }
}
