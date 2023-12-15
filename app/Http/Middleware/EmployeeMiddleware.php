<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->tokenCan('role:employee')) {
            return $next($request);
        }
        return response()->json(['error' => 'Un-Authorized Access'], 401);
    }
}
