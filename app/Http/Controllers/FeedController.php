<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class FeedController extends Controller
{
    /**
     * Fetch user feed
     *
     * - Find posts where author is in following + the users posts
     * - Sort by latest
     * - Limit to 30 per request
     * - Add count for reposts
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request) {
        $feed = Post::whereIn(
                'author',
                array_merge(
                    auth()->user()->following->pluck('following_user')->toArray(),
                    [auth()->user()->id]
                ))
            ->latest()
            ->limit(30)
            ->without('reposts')
            ->withCount('reposts');

        if ($request->offset) {
            $feed = $feed->where('id', '<', $request->offset);
        }

        // Get posts from users, where author is followed by authenticated user, ordered by date, limit 30
        return response()->json($feed->get());
    }
}
