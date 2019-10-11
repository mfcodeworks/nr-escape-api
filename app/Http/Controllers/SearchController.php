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

        if (isset($request->type) && $request->type == 'hashtag') {

            $topPosts = Post::whereDate('created_at', '>=', now()->subDays(1)->toDateTimeString())
                ->where('caption', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
                ->orderByRaw('(select count(*) from comments where reply_to = posts.id) desc')
                ->orderByRaw('(select count(*) from likes where post = posts.id) desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'recent' => array_merge(
                    Post::where('caption', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
                        ->latest()
                        ->get()
                        ->toArray(),
                    Comment::where('text', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
                        ->latest()
                        ->with('post')
                        ->get()
                        ->pluck('post')
                        ->toArray()
                ),
                'top' => array_merge(
                    $topPosts->toArray(),
                    Comment::whereNotIn('reply_to', $topPosts->pluck('id'))
                        ->whereDate('created_at', '>=', now()->subDays(1)->toDateTimeString())
                        ->where('text', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
                        ->groupBy('reply_to')
                        ->orderBy('reply_to', 'desc')
                        ->orderByRaw('(select count(*) from likes where post = comments.reply_to) desc')
                        ->with('post')
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
            ->get()
            ->pluck('caption')
            ->toArray();

        // Find comments containing matching hashtag
        $comments = DB::table('comments')
            ->select('text')
            ->where('text', 'REGEXP', $query['hash'].'[a-zA-Z\-\_]*')
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
