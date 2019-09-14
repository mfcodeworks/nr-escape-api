<?php

namespace App\Http\Middleware;

use App\User;
use App\Post;
use App\Comment;
use Illuminate\Support\Facades\Log;
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

        Log::alert(json_encode($request->route()));

        // Switch route to check variables
        switch ($request->route()->getName()) {
            case 'profile.show':
                $check = $request->route('profile');
                break;
            case 'comment.show':
                $check = Comment::findOrFail($request->route('comment'))->author;
                break;
            case 'post.show':
                $check = Post::findOrFail($request->route('post'))->author;
                break;
        }

        // Check if user has blocked, or been blocked, by profile
        if (auth()->user()->blocks->where('blocked_user', $check)->first()) {
            return response()->json([
                'error' => 'You have blocked this profile'
            ], 400);
        } elseif (User::findOrFail($check)->blocks->where('blocked_user', auth()->user()->id)->first()) {
            return response()->json([
                'error' => 'Profile has blocked you'
            ], 400);
        }

        return $next($request);
    }
}
