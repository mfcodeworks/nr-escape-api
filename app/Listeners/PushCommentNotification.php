<?php

namespace App\Listeners;

use App\Events\NewPostComment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use FCMGroup;

class PushCommentNotification
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
     * @param  NewPostComment $event
     * @return void
     */
    public function handle(NewPostComment $event)
    {
        // Get Post Author FCM Token
        $fcm_to = Post::where('id', $event->comment->reply_to)
            ->author()->pluck('fcm_token');

        // Get username that commented
        $username = User::where('id', $event->comment->author)
            ->first()
            ->pluck('username');

        // Create Notification
        $notification = (new PayloadNotificationBuilder())
            ->setTitle('New Comment')
            ->setBody("{$username} commented on your post")
            ->build();

        // Send Notification
        $response = FCM::sendTo($fcm_to, null, $notification, null);
    }
}
