<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EngagementScoreController extends Controller
{
    /**
     * Calculate users engagement score
     *
     * - foreach user post during last 9 months
     * - select count(likes, comments, reposts)
     * - (likes * like weightage + comments * comments weightage + reposts * repost weightage) * 100 to get percentage
     * - above percentage divided by user followers
     *
     * - Common weightage (likes:comments:reposts)
     * - Facebook 1:20:30
     * - Google+ 1:10:20
     */
}
