<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Repositories\SaleRepository;

class SaleController extends Controller
{

    public function __construct(SaleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sales = $this->repository->browse($request);

        return SaleResource::collection($sales);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $sale = $this->repository->store($request);

        return SaleResource::make($sale);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sale = $this->repository->show($id);

        return SaleResource::make($sale);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, $id)
    {
        $sale = $this->repository->update($id, $request);

        return SaleResource::make($sale);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->repository->destroy($id);

        return response('', 204);
    }
}
