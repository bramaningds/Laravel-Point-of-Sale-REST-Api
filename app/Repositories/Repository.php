<?php

namespace App\Repositories;

use Illuminate\Http\Request;

abstract class Repository
{

    public function browse(Request $request)
    {
        throw new Exception("Error Processing Request", 1);
    }

    public function create(Request $request)
    {
        throw new Exception("Error Processing Request", 1);
    }

    public function find($id)
    {
        throw new Exception("Error Processing Request", 1);
    }

    public function update($id, Request $request)
    {
        throw new Exception("Error Processing Request", 1);
    }

    public function destroy($id)
    {
        throw new Exception("Error Processing Request", 1);
    }

}
