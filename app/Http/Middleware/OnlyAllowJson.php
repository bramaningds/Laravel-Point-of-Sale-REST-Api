<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyAllowJson
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('get')) {
            // abort_if(!$request->wantsJson(), 406);
        } elseif ($request->isMethod('post')) {
            // abort_if(!$request->wantsJson(), 406);
            abort_if(!$request->isJson(), 406);
        } elseif ($request->isMethod('put')) {
            // abort_if(!$request->wantsJson());
            abort_if(!$request->isJson());
        } elseif ($request->isMethod('patch')) {
            // abort_if(!$request->wantsJson());
            abort_if(!$request->isJson());
        } elseif ($request->isMethod('delete')) {
            //
        }

        return $next($request);
    }
}
