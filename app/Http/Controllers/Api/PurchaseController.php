<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Base\PurchaseController as BasePurchaseController;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseResource;

class PurchaseController extends BasePurchaseController
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return PurchaseResource::collection(parent::index($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        return PurchaseResource::make(parent::store($request));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return PurchaseResource::make(parent::show($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, $id)
    {
        return PurchaseResource::make(parent::update($request, $id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return parent::destroy($id) ? response('', 204) : response('', 500);
    }
}
