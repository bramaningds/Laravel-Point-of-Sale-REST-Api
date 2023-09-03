<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Base\PurchaseItemController as BasePurchaseItemController;
use App\Http\Requests\StorePurchaseItemRequest;
use App\Http\Requests\UpdatePurchaseItemRequest;
use App\Http\Resources\ItemResource;

class PurchaseItemController extends BasePurchaseItemController
{

    /**
     * Display a listing of the resource.
     */
    public function index($purchase_id, Request $request)
    {
        return ItemResource::collection(parent::index($purchase_id, $request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($purchase_id, StorePurchaseItemRequest $request)
    {
        return ItemResource::make(parent::store($purchase_id, $request));
    }

    /**
     * Display the specified resource.
     */
    public function show($purchase_id, $product_id)
    {
        return ItemResource::make(parent::show($purchase_id, $product_id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $purchase_id, $product_id)
    {
        return ItemResource::make(parent::update($request, $purchase_id, $product_id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($purchase_id, $product_id)
    {
        return parent::destroy($purchase_id, $product_id) ? response('', 204) : response('', 500);
    }
}
