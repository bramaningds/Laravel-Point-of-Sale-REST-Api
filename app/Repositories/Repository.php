<?php

namespace App\Repositories;

use Exception;

use Illuminate\Http\Request;

abstract class Repository
{

    /**
     * Browse the resource
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function browse(Request $request)
    {
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Save a new resource and return the instance.
     *
     * @param  Request  $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(Request $request)
    {
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param  mixed  $id
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function find($id)
    {
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Update records in the database.
     *
     * @param  mixed  $id
     * @param  Request  $request
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function update($id, Request $request)
    {
        throw new Exception("Error Processing Request", 1);
    }

    /**
     * Destroy the models for the given id.
     *
     * @param  mixed  $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function destroy($id)
    {
        throw new Exception("Error Processing Request", 1);
    }

}
