<?php

namespace App\Http\Controllers\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Exceptions\ProductIsNotSellableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleItemRequest;
use App\Http\Requests\UpdateSaleItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Sale;
use App\Models\Product;

class SaleItemController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index($sale_id, Request $request)
    {
        $sale_items = Sale::with('items')->findOrFail($sale_id)->items;

        return $sale_items;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($sale_id, StoreSaleItemRequest $request)
    {
        // Find the sale
        $sale = Sale::with('items')->findOrFail($sale_id);

        // Find the product
        $product = Product::findOrFail($request->input('id'));

        // Throw if product is not sellable
        if ($product->isNotSellable()) throw new ProductIsNotSellableException($product);

        // Throw if product has insufficient required stock
        if ($product->hasInsufficientStock($request->input('quantity'))) throw new ProductHasInsufficientStock($product, $request->input('quantity'));

        try {
            // Begin transaction
            DB::beginTransaction();

            // Update the stock
            $product->decrement('stock', $request->input('quantity'));

            // Attach the product into sale
            $sale->items()->attach($product->id, [
                'quantity' => floatval($request->input('quantity')),
                'price' => floatval($request->input('price') ?? $product->price)
            ]);

            // Commit database
            DB::commit();

            $item = $sale->items()->find($product->id);
            // Makes laravel to return 201 http status code
            $item->wasRecentlyCreated = true;

            return $item;
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollback();
            // Throw the exception directly
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($sale_id, $product_id)
    {
        // Find the sale
        $sale = Sale::findOrFail($sale_id);
        // Find the item
        $item = $sale->items()->findOrFail($product_id);

        return $item;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $sale_id, $product_id)
    {
        // Find the sale
        $sale = Sale::findOrFail($sale_id);
        // Find the item
        $item = $sale->items()->findOrFail($product_id);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Update the stock
            $item->increment('stock', floatval($item->pivot->quantity) - floatval($request->input('quantity')));

            // Update the sale item
            $item->pivot->update([
                'quantity' => floatval($request->input('quantity') ?? $item->pivot->quantity),
                'price' => floatval($request->input('price') ?? $item->pivot->price)
            ]);

            // Commit database
            DB::commit();

            return ItemResource::make($item);
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollback();
            // Throw the exception directly
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($sale_id, $product_id)
    {
        // Find the sale
        $sale = Sale::findOrFail($sale_id);
        // Find the item
        $item = $sale->items()->findOrFail($product_id);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Update the stock
            $item->increment('stock', $item->pivot->quantity);
            // Detach relation
            $sale->items()->detach($product_id);

            // Commit database
            DB::commit();

            return true;
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollback();
            // Throw the exception directly
            throw $e;
        }
    }
}
