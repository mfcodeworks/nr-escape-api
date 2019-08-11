<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class CheckBlocked
{
    /**
     * Handle an outgoing request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get response from controller
        $response = $next($request);

        // Check for existing profile block
        $body = json_decode($response->getOriginalContent(), true);

        $block = auth()->user()
            ->blocks
            ->where('blocked_user', $body['id'])
            ->first();

        if (
            auth()->user()->blocks->where('blocked_user', $body['id'])->first() ||
            User::find($body['id'])->blocks->where('blocked_user', auth()->user()->id)->first()
        ) {
            return response()->json([
                'error' => 'Profile has been blocked'
            ], 400);
        }

        return $response;
    }
}
