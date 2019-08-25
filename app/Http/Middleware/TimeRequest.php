<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class TimeRequest
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
        // Handle request
        $response = $next($request);

        // Log request timing
        $timing = substr(microtime(true) - LARAVEL_START, 0, 7);
        Log::notice("Request time: {$timing} s");

        // Return response
        return $response;
    }
}
