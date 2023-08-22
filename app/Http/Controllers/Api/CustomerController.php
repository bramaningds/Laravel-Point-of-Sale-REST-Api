<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::with('last_order');

        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        $customers = $query->paginate();

        // return \DB::getQueryLog();
        // return $customers;
        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = new Customer;

        $customer->name = $request->input('name');
        $customer->phone = $request->input('phone');
        $customer->email = $request->input('email');
        $customer->address = $request->input('address');

        $customer->save();

        return CustomerResource::make($customer);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find the customer
        $customer = Customer::findOrFail($id);

        return CustomerResource::make($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        // Find the customer
        $customer = Customer::findOrFail($id);

        $customer->name = $request->input('name');
        $customer->phone = $request->input('phone');
        $customer->email = $request->input('email');
        $customer->address = $request->input('address');

        $customer->save();

        return CustomerResource::make($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the customer
        $customer = Customer::findOrFail($id);

        $customer->delete();

        return response(202);
    }
}
