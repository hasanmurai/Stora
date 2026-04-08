<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->isStaff()) {
            return $next($request);
        }

        return response()->json(['message' => 'Access Denied: High-level clearance required.'], 403);
    }
}
