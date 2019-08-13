<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get notifications for authenticated user
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        // Return notifications for authenticated user, within the last 4 weeks
        return auth()->user()
            ->notifications()
            ->where(
                'created_at',
                '>',
                Carbon::now()->subWeeks(env('USER_NOTIFICATIONS_PERIOD', 30))->toDateTimeString()
            )
            ->orderBy('created_at', 'desc');
    }

    /**
     * Store a newly created resource in storage.
     *
     * // TODO: Create a notification on post like, post comment, post repost, new follower, new @mention
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Select notification by ID
        return Notification::find($id);
    }
}
