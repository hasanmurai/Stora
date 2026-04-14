<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBannedStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // If the user is logged in AND their status is 'banned'
        if (Auth::check() && Auth::user()->status === 'banned') {

            return response()->json([
                'message' => 'Your account has been banned. Please contact support.'
            ], 403); // 403 Forbidden
        }

        return $next($request);
    }
}
