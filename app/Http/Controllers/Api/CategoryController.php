<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Base\CategoryController as BaseCategoryController;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends BaseCategoryController
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return CategoryResource::collection(parent::index($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        return CategoryResource::make(parent::store($request));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return CategoryResource::make(parent::show($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        return CategoryResource::make(parent::update($request, $id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return parent::destroy($id) ? response('', 204) : response('', 500);
    }
}
