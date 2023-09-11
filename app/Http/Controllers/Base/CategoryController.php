<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    /**
     * Paginate the category resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('keyword')) {
            $query->search($request->input('keyword'));
        }

        return $query->paginate();
    }

    /**
     * Store a new category resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Category
     *
     * @throws \Throwable
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = new Category;
        $category->name = $request->input('name');
        $category->saveOrFail();

        return $category;
    }

    /**
     * Display the category resource.
     *
     * @param  mixed  $id
     * @return \App\Models\Category
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show($id)
    {
        return Category::findOrFail($id);
    }

    /**
     * Update the category resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $id
     * @return \App\Models\Category
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException|\Throwable
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->name = $request->input('name', $category->name);
        $category->saveOrFail();

        return $category;
    }

    /**
     * Delete the category from the database within a transaction.
     *
     * @param  mixed  $id
     * @return bool|null
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException|\Throwable
     */
    public function destroy($id)
    {
        return Category::findOrFail($id, ['id'])->deleteOrFail();
    }
}
