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
     */

    public function __invoke() {
        $list = DB::table('following')
            ->select(
                'following.user',
                DB::Raw('count(*) as mutuals'),
                DB::Raw('(SELECT count(id) FROM posts WHERE posts.author = following.user AND posts.created_at > DATE_SUB(now(), INTERVAL '.env('RECOMMENDED_ACTIVITY_PERIOD', '1 YEAR').')) as activity'),
            )
            ->whereIn('following.following_user', auth()->user()->following->pluck('following_user'))
            ->where('following.user', '!=', auth()->user()->id)
            ->groupBy('following.user')
            ->havingRaw('activity > ?', [env('USER_RECOMMENDED_ACTIVITY', 10)])
            ->havingRaw('mutuals >= ?', [env('USER_RECOMMENDED_MUTUAL', 3)])
            ->orderBy('mutuals', 'desc')
            ->orderBy('activity', 'desc')
            ->get();

        // Transform userID to user object
        foreach ($list as $result) {
            $this->recommendations[] = User::find($result->user);
        }

        return response()->json($this->recommendations);
    }
}
