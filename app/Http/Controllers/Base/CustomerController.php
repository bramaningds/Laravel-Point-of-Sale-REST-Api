<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    /**
     * Paginate the customer resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $query = Customer::with('last_order', 'last_order.user', 'last_order.items');

        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        return $query->paginate();
    }

    /**
     * Store a new customer resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Customer
     *
     * @throws \Throwable
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = new Customer;

        $customer->name = $request->input('name');
        $customer->phone = $request->input('phone');
        $customer->email = $request->input('email');
        $customer->address = $request->input('address');

        $customer->saveOrFail();

        return $customer;
    }

    /**
     * Display the customer resource.
     *
     * @param  mixed  $id
     * @return \App\Models\Customer
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show($id)
    {
        return Customer::findOrFail($id);
    }

    /**
     * Update the customer resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $id
     * @return \App\Models\Customer
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException|\Throwable
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $customer->name = $request->input('name', $customer->name);
        $customer->phone = $request->input('phone', $customer->phone);
        $customer->email = $request->input('email', $customer->email);
        $customer->address = $request->input('address', $customer->address);

        $customer->saveOrFail();

        return $customer;
    }

    /**
     * Delete the customer from the database within a transaction.
     *
     * @param  mixed  $id
     * @return bool|null
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException|\Throwable
     */
    public function destroy($id)
    {
        return Customer::findOrFail($id, ['id'])->deleteOrFail();
    }
}
