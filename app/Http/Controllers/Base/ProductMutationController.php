<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductMutationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($id)
    {
        return Product::with('mutations')->findOrFail($id)->mutations;
    }
}
