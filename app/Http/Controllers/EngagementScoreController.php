<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EngagementScoreController extends Controller
{
    /**
     * Calculate users engagement score
     *
     * - foreach user post during last 6 months
     * - select count(likes, comments, reposts)
     * - (likes * like weightage + comments * comments weightage + reposts * repost weightage) * 100 to get percentage
     * - above percentage divided by user followers
     *
     * - Common weightage (likes:comments:reposts)
     * - Facebook 1:20:30
     * - Google+ 1:10:20
     */

    public function __invoke() {
        // Instantiate counts to 0
        $likesTotal = 0;
        $commentsTotal = 0;
        $repostsTotal = 0;

        // Get recent user posts
        $posts = auth()->user()
            ->posts
            ->where(
                'created_at',
                '>',
                Carbon::now()->subMonth(env('ENGAGEMENT_SCORE_PERIOD', 6))->toDateTimeString()
            );

        // Tally engagement total for recent posts
        foreach ($posts as $post) {
            $likesTotal += $post->likes_count;
            $commentsTotal += $post->comments_count;
            $repostsTotal += count($post->reposts);
        }

        // Calculate raw engagement number
        $engagementRaw = ($likesTotal * env('ENGAGEMENT_LIKE_WEIGHT', 1)) + ($commentsTotal * env('ENGAGEMENT_COMMENT_WEIGHT', 20)) + ($repostsTotal * env('ENGAGEMENT_REPOST_WEIGHT', 30));

        // Calculate engagement percentage based on followers
        auth()->user()->followers_count > 0
            ? $engagementScore = ($engagementRaw * 100) / auth()->user()->followers_count
            : $engagementScore = 0;

        // Return engagement score
        return response()->json([
            'score' => $engagementScore
        ], 201);
    }
}
