<?php

namespace App\Repositories;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product;

class ProductRepository extends Repository
{
    /**
     * Display a listing of the resource.
     */
    public function browse(Request $request)
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
    public function store(Request $request)
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
    public function update($id, Request $request)
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
