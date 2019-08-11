<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RecommendationsController extends Controller
{
    /**
     * TODO: Return array of recommendations for user
     *
     * Suggest recommendations based on mutual following.
     * - Select user
     * - where count(posts in last 6 months) as activity
     * - where count(user following and this.user following overlap) as mutual_following
     * - order by mutual_following desc
     * - order by activity desc
     */

    public function __invoke() {}
}
