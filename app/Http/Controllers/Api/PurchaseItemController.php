<?php

namespace App\Http\Controllers\Api;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Http\Requests\StorePurchaseItemRequest;
use App\Http\Requests\UpdatePurchaseItemRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;

class PurchaseItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($purchase_id)
    {
        // Find the purchase
        $purchase = Purchase::with('items')->findOrFail($purchase_id);

        return ItemResource::collection($purchase->items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseItemRequest $request, $purchase_id)
    {
        // Find the purchase record
        $purchase = Purchase::with('items')->findOrFail($purchase_id);

        // Find the product
        $product = Product::findOrFail($request->input('product_id'));
        // Throw exception if the product is not sellable
        if ($product->sellable == 'N') {
            throw new Exception('The product is not sellable');
        }
        // Throw an exception if the product has insufficient stcok
        if ($product->stock < floatval($request->input('quantity'))) {
            throw new Exception('The product has insufficient stock');
        }

        try {
            // Begin transaction
            DB::beginTransaction();

            // Update the stock
            $product->decrement('stock', $request->input('quantity'));

            // Attach the product into purchase
            $purchase->items()->attach($product, [
                'quantity' => floatval($request->input('quantity')),
                'price' => floatval($request->input('price') ?? $product->price)
            ]);

            // Commit database
            DB::commit();

            // return DB::getQueryLog();
            return ItemResource::make($purchase->items()->find($product->id));
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

        return ItemResource::make($item);
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

            return DB::getQueryLog();
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

            return response(202);
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollback();
            // Throw the exception directly
            throw $e;
        }
    }
}
