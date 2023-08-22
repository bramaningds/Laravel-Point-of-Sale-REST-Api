<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::with('last_order');

        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        $suppliers = $query->paginate();

        // return \DB::getQueryLog();
        // return $suppliers;
        return SupplierResource::collection($suppliers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request)
    {
        $supplier = new Supplier;

        $supplier->name = $request->input('name');
        $supplier->phone = $request->input('phone');
        $supplier->email = $request->input('email');
        $supplier->address = $request->input('address');

        $supplier->save();

        return SupplierResource::make($supplier);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find the supplier
        $supplier = Supplier::findOrFail($id);

        return SupplierResource::make($supplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, $id)
    {
        // Find the supplier
        $supplier = Supplier::findOrFail($id);

        $supplier->name = $request->input('name');
        $supplier->phone = $request->input('phone');
        $supplier->email = $request->input('email');
        $supplier->address = $request->input('address');

        $supplier->save();

        return SupplierResource::make($supplier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the supplier
        $supplier = Supplier::findOrFail($id);

        $supplier->delete();

        return response(202);
    }
}
