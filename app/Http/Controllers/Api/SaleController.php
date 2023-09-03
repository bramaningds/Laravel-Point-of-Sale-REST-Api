<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Base\SaleController as BaseSaleController;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;

class SaleController extends BaseSaleController
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return SaleResource::collection(parent::index($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        return SaleResource::make(parent::store($request));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return SaleResource::make(parent::show($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, $id)
    {
        return SaleResource::make(parent::update($request, $id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return parent::destroy($id) ? response('', 204) : response('', 500);
    }
}
