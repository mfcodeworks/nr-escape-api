<?php

namespace App\Listeners;

use App\Notification;
use App\Events\NewFollower;
use App\Following;
use App\FollowingRequest;
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

        // Don't create notificaton if same user
        if ($to == $from) return;

        // Create notification
        switch (true) {
            case $event->following instanceof FollowingRequest:
                break;

            case $event->following instanceof Following:
                Notification::create([
                    'for_author' => $to,
                    'from_user' => $from,
                    'type' => 'followed'
                ]);
                break;
        }
    }
}
