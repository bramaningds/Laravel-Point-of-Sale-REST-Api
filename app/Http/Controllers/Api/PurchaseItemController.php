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
    public function index($purchase_id)
    {
        return ItemResource::collection(parent::index($purchase_id));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseItemRequest $request, $purchase_id)
    {
        return ItemResource::make(parent::store($request, $purchase));        
    }

    /**
     * Display the specified resource.
     */
    public function show($purchase_id, $id)
    {
        return ItemResource::make(parent::show($purchase, $id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $purchase_id, $id)
    {
        return ItemResource::make(parent::update($request, $purchase, $id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($purchase_id, $id)
    {
        return parent::destroy($purchase, $id) ? response('', 204) : response('', 500);
    }
}
