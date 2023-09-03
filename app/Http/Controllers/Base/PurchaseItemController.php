<?php

namespace App\Http\Controllers\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseItemRequest;
use App\Http\Requests\UpdatePurchaseItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Purchase;
use App\Models\Product;

class PurchaseItemController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index($purchase_id, Request $request)
    {
        $purchase_items = Purchase::with('items')->findOrFail($purchase_id)->items;

        return $purchase_items;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($purchase_id, StorePurchaseItemRequest $request)
    {
        // Find the purchase
        $purchase = Purchase::with('items')->findOrFail($purchase_id);

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

            // Attach the product into purchase
            $purchase->items()->attach($product->id, [
                'quantity' => floatval($request->input('quantity')),
                'price' => floatval($request->input('price') ?? $product->price)
            ]);

            // Commit database
            DB::commit();

            $item = $purchase->items()->find($product->id);
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
    public function show($purchase_id, $product_id)
    {
        // Find the purchase
        $purchase = Purchase::findOrFail($purchase_id);
        // Find the item
        $item = $purchase->items()->findOrFail($product_id);

        return $item;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $purchase_id, $product_id)
    {
        // Find the purchase
        $purchase = Purchase::findOrFail($purchase_id);
        // Find the item
        $item = $purchase->items()->findOrFail($product_id);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Update the stock
            $item->increment('stock', floatval($item->pivot->quantity) - floatval($request->input('quantity')));

            // Update the purchase item
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
    public function destroy($purchase_id, $product_id)
    {
        // Find the purchase
        $purchase = Purchase::findOrFail($purchase_id);
        // Find the item
        $item = $purchase->items()->findOrFail($product_id);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Update the stock
            $item->increment('stock', $item->pivot->quantity);
            // Detach relation
            $purchase->items()->detach($product_id);

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
