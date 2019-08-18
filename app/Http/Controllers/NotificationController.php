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
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * // TODO: Create a notification on post like, post comment, post repost, new follower, new @mention
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // Validate post
        $validator = Validator::make($request->all(), [
            'for_author' => 'required|exists:users,id',
            'from_user' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'type' => 'string',
        ]);
        if ($validator->fails()) {
            // TODO: Send error to admin
        }

        return Notification::create($request->all());
    }

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
