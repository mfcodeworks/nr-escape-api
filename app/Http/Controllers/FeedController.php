<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class FeedController extends Controller
{
    /**
     * Fetch user feed
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request) {
        // Get posts from users, where author is followed by authenticated user, ordered by date, limit 30
        return response()->json(
            Post::whereIn('author', auth()->user()->following->pluck('following_user'))
                ->latest()
                ->limit(30)
                ->without('reposts')
                ->withCount('reposts')
                ->get()
        );
    }
}
