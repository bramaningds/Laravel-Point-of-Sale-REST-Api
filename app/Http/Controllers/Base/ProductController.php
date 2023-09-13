<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    /**
     * Paginate the products resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        if ($request->filled('category_id')) {
            $query->ofCategory($request->input('category_id'));
        }

        return $query->paginate();
    }

    /**
     * Store a new product resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Product
     *
     * @throws \Throwable
     */
    public function store(StoreProductRequest $request)
    {
        $product = new Product;

        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->stock = 0;
        $product->sellable = $request->input('sellable', 'Y');
        $product->purchasable = $request->input('purchasable', 'Y');

        $product->saveOrFail();

        return $product;
    }

    /**
     * Display the specified product resource.
     *
     * @param  mixed  $id
     * @return \App\Models\Product
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Update the product resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $id
     * @return \App\Models\Product
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException|\Throwable
     */
    public function update(UpdateProductRequest $request, $id)
    {
        // Find the product
        $product = Product::findOrFail($id);

        $product->name = $request->input('name', $product->name);
        $product->description = $request->input('description', $product->description);
        $product->price = $request->input('price', $product->price);
        $product->stock = $request->input('stock', $product->stock);
        $product->sellable = $request->input('sellable', $product->sellable);
        $product->purchasable = $request->input('purchasable', $product->purchasable);

        $product->saveOrFail();

        return $product;
    }

    /**
     * Delete the product from the database within a transaction.
     *
     * @param  mixed  $id
     * @return bool|null
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException|\Throwable
     */
    public function destroy($id)
    {
        return Product::findOrFail($id, ['id'])->deleteOrFail();
    }
}
