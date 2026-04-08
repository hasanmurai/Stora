<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next){

        if (Auth::guard('api')->check() && Auth::guard('api')->user()->role === 'admin') {
            return $next($request);
        }

        return response()->json(['message' => 'Access Denied: Admins Only'], 403);
    }
}
