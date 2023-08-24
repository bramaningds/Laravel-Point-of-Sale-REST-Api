<?php

namespace App\Repositories;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Supplier;

class SupplierRepository extends Repository
{

    /**
     * Display a listing of the resource.
     */
    public function browse(Request $request)
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
    public function store(Request $request)
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
    public function update($id, Request $request)
    {
        $supplier = Supplier::findOrFail($id);

        $supplier->name = $request->input('name');
        $supplier->phone = $request->input('phone');
        $supplier->email = $request->input('email');
        $supplier->address = $request->input('address');

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