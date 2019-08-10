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
        return response()->json(
            auth()->user()
                ->notifications()
                ->where(
                    'created_at',
                    '>',
                    Carbon::now()->subWeek(4)->toDateTimeString()
                )
                ->orderBy('created_at', 'desc'),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // TODO: Create new notification
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
