<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecommendationsController extends Controller
{
    private $recommendations = [];

    /**
     * Return array of recommendations for user
     *
     * Suggest recommendations based on mutual following.
     * - Select user ID
     * - where count(posts in last 1 year) as activity > 10 posts
     * - where count(user following and this.user following overlap) as mutual_following >= 3 mutual following
     * - order by mutual_following desc
     * - order by activity desc
     *
     * @return \Illuminate\Http\Response
     */

    public function __invoke() {
        // Get a list of user ids
        $list = DB::table('following')
            ->select(
                'following.user as user',
                DB::Raw('count(*) as mutuals'),
                DB::Raw('(SELECT count(id) FROM posts WHERE posts.author = following.user AND posts.created_at > DATE_SUB(now(), INTERVAL '.env('RECOMMENDED_ACTIVITY_PERIOD', '1 YEAR').')) as activity'),
            )
            ->whereNotIn('following.user', auth()->user()->followers->pluck('user'))
            ->whereIn('following.following_user', auth()->user()->following->pluck('following_user'))
            ->where('following.user', '!=', auth()->user()->id)
            ->groupBy('following.user')
            ->havingRaw('activity > ?', [env('USER_RECOMMENDED_ACTIVITY', 10)])
            ->havingRaw('mutuals >= ?', [env('USER_RECOMMENDED_MUTUAL', 3)])
            ->orderBy('mutuals', 'desc')
            ->orderBy('activity', 'desc')
            ->limit(30)
            ->get();

        // Get a list of user ids without any mutuals
        if (!$list->first()) {
            $list = DB::table('users')
                ->select(
                    'id as user',
                    DB::Raw('(SELECT count(id) FROM posts WHERE posts.author = users.id AND posts.created_at > DATE_SUB(now(), INTERVAL '.env('RECOMMENDED_ACTIVITY_PERIOD', '1 YEAR').')) as activity'),
                )
                ->groupBy('user')
                ->havingRaw('activity > ?', [env('USER_RECOMMENDED_ACTIVITY', 10)])
                ->orderBy('activity', 'desc')
                ->limit(30)
                ->get();

        }

        // Transform userID to user object
        $this->recommendations = User::with('recentPosts')
            ->findOrFail($list->pluck('user')->toArray())
            ->pluck('recentPosts');

        return response()->json($this->recommendations->shuffle());
    }
}
