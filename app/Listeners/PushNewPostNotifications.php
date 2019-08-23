<?php

namespace App\Listeners;

use App\User;
use App\Events\NewPost;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use FCM;
use FCMGroup;

class PushNewPostNotifications
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
     * @param  NewPost  $event
     * @return void
     */
    public function handle(NewPost $event)
    {
        // Get Post Author FCM Token
        $author_id = $event->post->author;

        // Get username that commented
        $username = User::where('id', $author_id)
            ->first()
            ->value('username');

        // Send to topic for this user
        $topic = (new Topics())->topic("{$author_id}.posts");

        // Create Notification
        $notification = (new PayloadNotificationBuilder())
            ->setTitle('New Post')
            ->setBody("{$username} has a new post")
            ->build();

        // Send Notification
        $response = FCM::sendToTopic($topic, null, $notification, null);
    }
}
