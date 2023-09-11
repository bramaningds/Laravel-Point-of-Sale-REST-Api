<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Base\SaleItemController as BaseSaleItemController;
use App\Http\Requests\StoreSaleItemRequest;
use App\Http\Requests\UpdateSaleItemRequest;
use App\Http\Resources\ItemResource;

class SaleItemController extends BaseSaleItemController
{

    /**
     * Display a listing of the resource.
     */
    public function index($sale_id, Request $request)
    {
        return ItemResource::collection(parent::index($sale_id, $request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($sale_id, StoreSaleItemRequest $request)
    {
        return ItemResource::make(parent::store($sale_id, $request));        
    }

    /**
     * Display the specified resource.
     */
    public function show($sale_id, $product_id)
    {
        return ItemResource::make(parent::show($sale_id, $product_id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $sale_id, $product_id)
    {
        return ItemResource::make(parent::update($request, $sale_id, $product_id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($sale_id, $product_id)
    {
        return parent::destroy($sale_id, $product_id) ? response('', 204) : response('', 500);
    }
}
