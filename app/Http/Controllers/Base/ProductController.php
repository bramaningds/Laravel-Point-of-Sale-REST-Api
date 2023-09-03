<?php

namespace App\Http\Controllers\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        $products = $query->paginate();

        return $products;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = new Product;

        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->stock = 0;
        $product->sellable = $request->input('sellable', 'Y');
        $product->purchasable = $request->input('purchasable', 'Y');

        $product->save();

        return $product;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find the product
        $product = Product::findOrFail($id);

        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        // Find the product
        $product = Product::findOrFail($id);

        $product->name = $request->input('name', $product->name);
        $product->description = $request->input('description', $product->description);
        $product->price = $request->input('price', $product->price);
        $product->sellable = $request->input('sellable', $product->sellable);
        $product->purchasable = $request->input('purchasable', $product->purchasable);

        $product->save();

        return $product;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the product
        $product = Product::findOrFail($id);

        return $product->delete();
    }
}
