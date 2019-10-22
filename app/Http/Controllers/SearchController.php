<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;
use App\Post;
use App\Comment;
use Carbon\Carbon;

class SearchController extends Controller
{
    public function __invoke(Request $request) {
        // Format query
        $query = $this->formatQuery($request->input('query'));

        // If the request type is hashtag, find the posts containing this hashtag
        if (isset($request->type) && $request->type == 'hashtag') {
            /**
             * Find posts
             * - within the past 24 hours
             * - with the most comments
             * - then with the most likes
             * - then the most recent
             * - limit to top 30 posts
             */
            $topPosts = Post::whereDate('created_at', '>=', now()->subDays(1)->toDateTimeString())
                ->where('caption', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
                ->orderByRaw('(select count(*) from comments where reply_to = posts.id) desc')
                ->orderByRaw('(select count(*) from likes where post = posts.id) desc')
                ->limit(30)
                ->latest();

            $topComments = Comment::whereDate('created_at', '>=', now()->subDays(1)->toDateTimeString())
                ->where('text', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
                ->groupBy('reply_to')
                ->orderBy('reply_to', 'desc')
                ->orderByRaw('(select count(*) from likes where post = comments.reply_to) desc')
                ->with('post')
                ->limit(30)
                ->latest();

            $recentPosts = Post::where('caption', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
                ->limit(30)
                ->latest();

            $recentComments = Comment::where('text', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
                ->limit(30)
                ->with('post')
                ->latest();

            /**
             * Add offsets if needed
             */
            if ($request->topNotIn) {
                $topPosts->where('id', '>', json_decode($request->topNotIn));
                $topComments->where('repy_to', '>', json_decode($request->topNotIn));
            }
            if ($request->recentNotIn) {
                $recentPosts->where('id', '>', json_decode($request->topNotIn));
                $recentComments->where('repy_to', '>', json_decode($request->topNotIn));
            }

            /**
             * Fetch posts and comments from the built queries, then merge the arrays and return
             *
             */
            return response()->json([
                'recent' => array_merge(
                    $recentPosts->get()
                        ->toArray(),
                    $recentComments->whereNotIn('reply_to', $recentPosts->pluck('id'))
                        ->get()
                        ->pluck('post')
                        ->toArray()
                ),
                'top' => array_merge(
                    $topPosts->get()
                        ->toArray(),
                    $topComments->whereNotIn('reply_to', $topPosts->pluck('id'))
                        ->get()
                        ->pluck('post')
                        ->toArray()
                )
            ]);
        }

        // Instantiate tags array
        $tags = [];

        // Find posts containing matching hashtag
        $posts = DB::table('posts')
            ->select('caption')
            ->where('caption', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
            ->limit(50)
            ->get()
            ->pluck('caption')
            ->toArray();

        // Find comments containing matching hashtag
        $comments = DB::table('comments')
            ->select('text')
            ->where('text', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
            ->groupBy('reply_to')
            ->orderBy('reply_to', 'desc')
            ->limit(50)
            ->get()
            ->pluck('text')
            ->toArray();

        // Filter tags from matches
        $list = array_merge($posts, $comments);
        foreach($list as $tag) {
            preg_match_all("/{$query['hash']}[a-zA-Z\-\_0-9]*/i", $tag, $t);
            $tags = array_merge($t[0], $tags);
        }

        // Find users matching query
        $users = User::where('username', 'like', $query['user'])
            ->limit(15)
            ->get();

        // Remove users that are blocked from viewing
        for ($i = 0; $i < count($users); $i++) {
            if (!auth()->user()->can('view', $users[$i])) {
                unset($users[$i]);
            }
        }

        // Return match array
        return response()->json([
            'users' => $users,
            'hashtags' => array_values(array_unique($tags))
        ]);
    }

    /**
     * Format search query
     *
     * @param string $query
     * @return string
     */
    private function formatQuery($query) {
        if ($query[0] === '#') $query = substr($str, 1);
        return [
            'hash' => '#'.str_replace(' ', '%', $query),
            'user' => '%'.str_replace(' ', '%', $query).'%'
        ];
    }
}
