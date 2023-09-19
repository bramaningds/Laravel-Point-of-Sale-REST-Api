<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Base\ProductMutationController as BaseProductMutationController;
use App\Http\Resources\ProductMutationResource;

class ProductMutationController extends BaseProductMutationController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($id)
    {
        return ProductMutationResource::collection(parent::__invoke($id));
    }
}
