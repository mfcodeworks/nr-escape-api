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
        $notifications = auth()->user()
            ->notifications()
            ->with('comment', 'post')
            ->whereDate(
                'created_at',
                '>',
                Carbon::now()->subWeeks(env('USER_NOTIFICATIONS_PERIOD', 30))->toDateTimeString()
            )
            ->latest();

        if ($request->offset) {
            $notifications = auth()->user()
                ->notifications()
                ->with('comment', 'post')
                ->where('id', '<', $request->offset)
                ->limit(30)
                ->latest();
        }

        // Return notifications for authenticated user, within the last 4 weeks
        return response()->json($notifications->get());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Select notification by ID
        return response()->json(Notification::findOrFail($id));
    }
}
