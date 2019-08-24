<?php

namespace App\Listeners;

use App\Notification;
use App\Events\NewFollower;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateFollowerNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewFollower  $event
     * @return void
     */
    public function handle(NewFollower $event)
    {
        // Get related users
        $to = $event->following->following_user;
        $from = $event->following->user;

        // Create notification
        Notification::create([
            'for_author' => $to,
            'from_user' => $from,
            'type' => 'followed'
        ]);
    }
}
