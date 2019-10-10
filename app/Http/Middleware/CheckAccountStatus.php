<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if user account is deactivated
        if (auth()->user()->deactivated) {
            return response()->json([
                'error' => 'Account has been deactivated, login again to reactivate'
            ], 401);
        } else if (auth()->user()->banned_until && now()->lessThan(auth()->user()->banned_until)) {
            return response()->json([
                'error' => 'Account has been suspended for ' . now()->diffInDays(auth()->user()->banned_until) . ' days'
            ], 401);
        }

        return $next($request);
    }
}
