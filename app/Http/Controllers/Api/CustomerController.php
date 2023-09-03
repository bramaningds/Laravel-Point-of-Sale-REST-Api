<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Base\CustomerController as BaseCustomerController;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;

class CustomerController extends BaseCustomerController
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return CustomerResource::collection(parent::index($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        return CustomerResource::make(parent::store($request));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return CustomerResource::make(parent::show($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        return CustomerResource::make(parent::update($request, $id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return parent::destroy($id) ? response('', 204) : response('', 500);
    }
}
