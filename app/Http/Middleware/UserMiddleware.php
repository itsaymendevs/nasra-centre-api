<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
 
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->tokenCan('role:user')) {
            return $next($request);
        }
        return response()->json(['error' => 'Un-Authorized Access'], 401);
    }
}
