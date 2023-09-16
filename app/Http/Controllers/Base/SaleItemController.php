<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleItemRequest;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleItemController extends Controller
{

    /**
     * Show all sale items
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index($sale_id)
    {
        return Sale::with('items')->findOrFail($sale_id)->items;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleItemRequest $request, $sale_id)
    {
        // Find the sale
        $sale = Sale::findOrFail($sale_id);
        // Find the product
        $product = Product::find($request->input('id'));

        try {
            // Begin transaction
            DB::beginTransaction();

            // Update the stock
            $product->decrement('stock', $request->input('quantity'));

            // Attach the product into sale
            $sale->items()->attach($product->id, [
                'quantity' => floatval($request->input('quantity')),
                'price' => floatval($request->input('price') ?? $product->price),
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
    public function show($sale_id, $id)
    {
        return Sale::findOrFail($sale_id)->items()->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $sale_id, $id)
    {
        // Find the sale
        $sale = Sale::findOrFail($sale_id);
        // Find the item
        $item = $sale->items()->findOrFail($id);

        // If nothing chages then return
        if ($item->pivot->quantity == $request->input('quantity', $item->pivot->quantity)
            && $item->pivot->price == $request->input('price', $item->pivot->price)) {
            return $item;
        }

        try {
            // Begin transaction
            DB::beginTransaction();

            // Update the stock
            $item->increment('stock', floatval($item->pivot->quantity) - floatval($request->input('quantity')));

            // Update the pivot
            $item->pivot->update([
                'quantity' => floatval($request->input('quantity') ?? $item->price),
                'price' => floatval($request->input('price') ?? $item->price),
            ]);

            // Touch the sale record
            $sale->touch();

            // Commit database
            DB::commit();

            return $item;
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
    public function destroy($sale_id, $id)
    {
        // Find the sale
        $sale = Sale::findOrFail($sale_id);
        // Find the item
        $item = $sale->items()->findOrFail($id);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Update the stock
            $item->increment('stock', $item->pivot->quantity);

            // Mark as deleted
            $item->pivot->update(['deleted_at' => now()]);

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
