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
    public function handle($request, Closure $next) {
        // Instantiate variable to check
        $check = 0;

        // Get response from controller
        $response = $next($request);

        // Switch route to check variables
        switch ($request->route()->getName()) {
            case 'profile.show':
                // Get response body
                $body = json_decode($response->getOriginalContent(), true);
                $check = $body['id'];
                break;
            case 'comment.show':
            case 'post.show':
                // Get response body
                $body = json_decode($response->getOriginalContent(), true);
                $check = $body['author']['id'];
                break;
        }

        // Check if user has blocked, or been blocked, by profile
        if (
            auth()->user()->blocks->where('blocked_user', $check)->first() ||
            User::find($check)->blocks->where('blocked_user', auth()->user()->id)->first()
        ) {
            return response()->json([
                'error' => 'Profile has been blocked'
            ], 400);
        }

        return $response;
    }
}
