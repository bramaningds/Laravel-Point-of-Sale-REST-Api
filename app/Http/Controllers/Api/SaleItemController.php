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
    public function index($sale_id)
    {
        return ItemResource::collection(parent::index($sale_id));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleItemRequest $request, $sale_id)
    {
        return ItemResource::make(parent::store($request, $sale_id));        
    }

    /**
     * Display the specified resource.
     */
    public function show($sale_id, $id)
    {
        return ItemResource::make(parent::show($sale, $id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $sale_id, $id)
    {
        return ItemResource::make(parent::update($request, $sale, $id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($sale_id, $id)
    {
        return parent::destroy($sale, $id) ? response('', 204) : response('', 500);
    }
}
