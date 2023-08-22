<?php

namespace App\Http\Controllers\Api;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\User;
use App\Repositories\PurchaseRepository;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Make purchases query object
        $query = Purchase::query();

        // Search purchases in user, supplier, or product
        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        // Filter purchases by user
        if ($request->filled('user_id')) {
            $query->ofUser($request->input('user_id'));
        }

        // Filter purchases by supplier
        if ($request->filled('supplier_id')) {
            $query->ofSupplier($request->input('supplier_id'));
        }

        // Filter purchases by product
        if ($request->filled('product_id')) {
            $query->ofProduct($request->input('product_id'));
        }

        // Filter purchases by date range
        if ($request->filled('date')) {
            [$date_start, $date_end] = explode(':', $request->input('date'));

            $query->ofDate($date_start, $date_end ?? $date_start);
        }

        $purchases = $query->paginate();

        // return \DB::getQueryLog();
        return PurchaseResource::collection($purchases);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        try {
            // Begin transaction
            DB::beginTransaction();

            // Find the user
            $user = User::findOrFail($request->input('user_id'));

            // Find or create the supplier
            $supplier = $request->filled('supplier_id')
                // Find supplier or throw exception
                ? Supplier::findOrFail($request->input('supplier_id'))
                // Create new supplier
                : Supplier::create([
                    'name' => $request->input('supplier.name'),
                    'email' => $request->input('supplier.email'),
                    'phone' => $request->input('supplier.phone'),
                    'address' => $request->input('supplier.address'),
                ]);

            // Create new purchase
            $purchase = new Purchase;
            // Set the user
            $purchase->user()->associate($user);
            // Set the supplier
            $purchase->supplier()->associate($supplier);
            // Save the purchase
            $purchase->save();
            // Set the purchase items
            $purchase->items()->attach($request->input('items'));
            // Decrement product items stock
            $purchase->items->each(function($product) {
                $product->increment('stock', $product->pivot->quantity);
            });

            // Commit database
            DB::commit();

            // return DB::getQueryLog();
            return PurchaseResource::make($purchase);

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
    public function show($id)
    {
        // Find the purchase or fail
        $purchase = Purchase::findOrFail($id);
        // return the resource
        return PurchaseResource::make($purchase);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, $id)
    {
        // Find the purchase
        $purchase = Purchase::findOrFail($id);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Set the purchase user if exists user_id in request
            if ($request->filled('user_id')) {
                // Set the purchase user
                $purchase->user()->associate(
                    // Find the user or throw exception if not found
                    User::findOrFail($request->input('user_id'))
                );
            }

            // Set the purchase user if request has and filled supplier_id field
            if ($request->filled('supplier_id')) {
                // Set the purchase user
                $purchase->supplier()->associate(
                    // Find or create the supplier
                    Supplier::findOrFail($request->input('supplier_id'))
                );

            } elseif ($request->filled('supplier')) {
                // Set the purchase user
                $purchase->supplier()->associate(
                    // Create the supplier
                    $supplier = Supplier::create([
                        'name' => $request->input('supplier.name'),
                        'email' => $request->input('supplier.email'),
                        'phone' => $request->input('supplier.phone'),
                        'address' => $request->input('supplier.address'),
                    ])
                );

            }

            // Save the purchase record to database
            $purchase->save();

            // Commit database
            DB::commit();

            // return DB::getQueryLog();
            return new PurchaseResource($purchase);
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
    public function destroy($id)
    {
        try {
             // Find the purchase
            $purchase = Purchase::with('items')->findOrFail($id);

             // Begin transaction
            DB::beginTransaction();

            // Increment the stock used
            $purchase->items->each(function($product) {
                $product->decrement('stock', $product->pivot->quantity);
            });
            // Remove the purchase record
            $purchase->delete();

            // Commit transaction
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