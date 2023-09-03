<?php

namespace App\Http\Controllers\Base;

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
        $query = Supplier::with('last_order', 'last_order.user', 'last_order.items');

        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        $suppliers = $query->paginate();

        return $suppliers;
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

        return $supplier;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);

        return $supplier;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $supplier->name = $request->input('name', $supplier->name);
        $supplier->phone = $request->input('phone', $supplier->phone);
        $supplier->email = $request->input('email', $supplier->email);
        $supplier->address = $request->input('address', $supplier->address);

        $supplier->save();

        return $supplier;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id, ['id']);

        return $supplier->delete();
    }
}
