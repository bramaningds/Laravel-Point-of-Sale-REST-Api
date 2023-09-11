<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::with('user', 'customer', 'items');

        // Search sales in user, customer, or product
        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        // Filter sales by user
        if ($request->filled('user_id')) {
            $query->ofUser($request->input('user_id'));
        }

        // Filter sales by customer
        if ($request->filled('customer_id')) {
            $query->ofCustomer($request->input('customer_id'));
        }

        // Filter sales by product
        if ($request->filled('product_id')) {
            $query->ofProduct($request->input('product_id'));
        }

        // Filter sales by date range
        if ($request->filled('date')) {
            [$date_start, $date_end] = explode(':', $request->input('date'));

            $query->ofDate($date_start, $date_end ?? $date_start);
        }

        $sales = $query->paginate();

        return $sales;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        try {
            // Begin transaction
            DB::beginTransaction();

            // Find the user
            $user = User::find($request->input('user_id'));

            // Find or create the customer
            $customer = $request->filled('customer_id')
                // Find customer or throw exception
                ? Customer::find($request->input('customer_id'))
                // Create new customer
                : Customer::create([
                    'name' => $request->input('customer.name'),
                    'email' => $request->input('customer.email'),
                    'phone' => $request->input('customer.phone'),
                    'address' => $request->input('customer.address'),
                ]);

            // Makes array of items in order to be attached to sale record
            $items = array_reduce($request->input('items'), function ($items, $item) {
                // Find the product
                $product = Product::find($item['id']);
                // Decrement product items stock
                $product->decrement('stock', $item['quantity']);
                // Set the item as in forms of attaching item requirement
                $items[$product->id] = [
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? $product->price,
                ];

                return $items;
            }, []);

            // Create new sale
            $sale = new Sale;
            // Set the user
            $sale->user()->associate($user);
            // Set the customer
            $sale->customer()->associate($customer);
            // Save the sale
            $sale->save();
            // Set the sale items
            $sale->items()->attach($items);

            // Commit database
            DB::commit();

            // return DB::getQueryLog();
            return $sale->load('items');

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
        // Find the sale or fail
        $sale = Sale::find($id);
        // return the resource
        return $sale;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, $id)
    {
        // Find the sale
        $sale = Sale::find($id);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Set the sale user if exists user_id in request
            if ($request->filled('user_id')) {
                // Set the sale user
                $sale->user()->associate(
                    // Find the user or throw exception if not found
                    User::find($request->input('user_id'))
                );
            }

            // Set the sale user if request has and filled customer_id field
            if ($request->filled('customer_id')) {
                // Set the sale user
                $sale->customer()->associate(
                    // Find or create the customer
                    Customer::find($request->input('customer_id'))
                );

            } elseif ($request->filled('customer')) {
                // Set the sale user
                $sale->customer()->associate(
                    // Create the customer
                    $customer = Customer::create([
                        'name' => $request->input('customer.name'),
                        'email' => $request->input('customer.email'),
                        'phone' => $request->input('customer.phone'),
                        'address' => $request->input('customer.address'),
                    ])
                );

            }

            // Save the sale record to database
            $sale->save();

            // Commit database
            DB::commit();

            // return DB::getQueryLog();
            return $sale;
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
            // Find the sale
            $sale = Sale::with('items')->find($id, ['id']);

            // Begin transaction
            DB::beginTransaction();

            // Increment the stock used
            $sale->items->each(function ($product) {
                $product->increment('stock', $product->pivot->quantity);
            });
            // Remove the sale record
            $sale->delete();

            // Commit transaction
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
