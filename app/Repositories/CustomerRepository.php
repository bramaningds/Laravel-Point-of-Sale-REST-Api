<?php

namespace App\Repositories;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Customer;

class CustomerRepository extends Repository
{

    /**
     * Display a listing of the resource.
     */
    public function browse(Request $request)
    {
        $query = Customer::with('last_order', 'last_order.user', 'last_order.items');

        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        $customers = $query->paginate();

        return $customers;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customer = new Customer;

        $customer->name = $request->input('name');
        $customer->phone = $request->input('phone');
        $customer->email = $request->input('email');
        $customer->address = $request->input('address');

        $customer->save();

        return $customer;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        return $customer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $customer = Customer::findOrFail($id);

        $customer->name = $request->input('name');
        $customer->phone = $request->input('phone');
        $customer->email = $request->input('email');
        $customer->address = $request->input('address');

        $customer->save();

        return $customer;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id, ['id']);

        return $customer->delete();
    }
}