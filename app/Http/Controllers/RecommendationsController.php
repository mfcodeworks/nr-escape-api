<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecommendationsController extends Controller
{
    /**
     * Return array of recommendations for user
     *
     * Suggest recommendations based on mutual following.
     * - Select user ID
     * - where count(posts in last 6 months) as activity > 1 post per month (6)
     * - where count(user following and this.user following overlap) as mutual_following
     * - order by mutual_following desc
     * - order by activity desc
     */

    public function __invoke() {
        $list = DB::table('following')
            ->select(
                'following.user',
                DB::Raw('count(*) as mutuals'),
                DB::Raw('(SELECT count(id) FROM posts WHERE posts.author = following.user AND posts.created_at > DATE_SUB(now(), INTERVAL 6 MONTH)) as activity')
            )
            ->whereIn('following.following_user', auth()->user()->following->pluck('following_user'))
            ->groupBy('following.user')
            ->having('activity', '>', env('USER_RECOMMENDED_ACTIVITY_LIMIT', 2))
            ->orderBy('mutuals', 'desc')
            ->orderBy('activity', 'desc')
            ->get();

        foreach ($list as $result) {
            $recommendations[] = User::find($result->user);
        }

        // TODO: Transform userID to user object

        return response()->json($recommendations);
    }
}
