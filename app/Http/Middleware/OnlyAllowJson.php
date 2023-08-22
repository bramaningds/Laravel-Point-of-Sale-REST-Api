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
        if ($request->isMethod('post')) {
            abort_if(! $request->isJson(), 406, 'Not acceptable');
        }
        elseif ($request->isMethod('put')) {
            abort_if(! $request->isJson(), 406, 'Not acceptable');
        }

        return $next($request);
    }
}
