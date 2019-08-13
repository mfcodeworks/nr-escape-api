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
                Carbon::now()->subWeeks(4)->toDateTimeString()
            )
            ->orderBy('created_at', 'desc');
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
