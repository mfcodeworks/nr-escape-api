<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class FeedController extends Controller
{
    // Fetch user feed
    public function __invoke(Request $request) {
        // Get posts from users, where author is followed by authenticated user, ordered by date, limit 30
        $posts = Post::whereIn('author', auth()->user()->following->pluck('following_user'))
            ->latest()
            ->limit(30)
            ->withCount('reposts')
            ->without('reposts')
            ->get();

        // Return feed
        return response()->json($posts, 200);
    }
}
