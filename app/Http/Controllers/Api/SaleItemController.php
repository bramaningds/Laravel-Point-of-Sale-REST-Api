<?php

namespace App\Http\Controllers\Api;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Http\Requests\StoreSaleItemRequest;
use App\Http\Requests\UpdateSaleItemRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;

class SaleItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($sale_id)
    {
        // Find the sale
        $sale = Sale::with('items')->findOrFail($sale_id);

        return ItemResource::collection($sale->items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleItemRequest $request, $sale_id)
    {
        // Find the sale record
        $sale = Sale::with('items')->findOrFail($sale_id);

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

            // Attach the product into sale
            $sale->items()->attach($product, [
                'quantity' => floatval($request->input('quantity')),
                'price' => floatval($request->input('price') ?? $product->price)
            ]);

            // Commit database
            DB::commit();

            // return DB::getQueryLog();
            return ItemResource::make($sale->items()->find($product->id));
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

        return ItemResource::make($item);
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

            return response(202);
        } catch (Exception $e) {
            // Rollback transaction
            DB::rollback();
            // Throw the exception directly
            throw $e;
        }
    }
}
