<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Repositories\PurchaseRepository;

class PurchaseController extends Controller
{

    public function __construct(PurchaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $purchases = $this->repository->browse($request);

        return PurchaseResource::collection($purchases);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        $purchase = $this->repository->store($request);

        return PurchaseResource::make($purchase);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $purchase = $this->repository->show($id);

        return PurchaseResource::make($purchase);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, $id)
    {
        $purchase = $this->repository->update($id, $request);

        return PurchaseResource::make($purchase);
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
