<?php

namespace App\Listeners;

use App\User;
use App\Events\NewFollower;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use FCMGroup;

class PushFollowerNotification
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'push';

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
        // Get Post Author FCM Token
        $fcm_to = User::where('id', $event->following->following_user)
            ->first()
            ->value('fcm_token');

        // Get username that followed
        $username = User::where('id', $event->following->user)
            ->first()
            ->value('username');

        // Create Notification
        $notification = (new PayloadNotificationBuilder())
            ->setTitle('New Follower')
            ->setBody("{$username} followed you")
            ->build();

        // Send Notification
        $response = FCM::sendToGroup($fcm_to, null, $notification, null);
    }
}
