<?php

namespace App\Listeners;

use App\User;
use Illuminate\Facade\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use FCMGroup;

class CheckForUserMentions implements ShouldQueue
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
     * Check post text for user mention (@user)
     * Regex check: /\B(\@[a-zA-Z\-\_]+\b)/
     * Foreach result
     *   user = User::where('username', $result);
     * Create notification
     * Push notification
     *
     * @param $event
     * @return void
     */
    public function handlePost($event)
    {
        // DEBUG: Log event
        Log::notice($event->post);

        // Scan Post Caption
        $matches;
        preg_match('/\B(\@[0-9a-zA-Z\-\_]+\b)/', $event->post->caption, $matches);

        // Get username of OP
        $username = User::find($event->post->author)
            ->value('username');

        // Notify each match of tag
        foreach ($matches as $match) {
            Log::notice("Matched user tag {$match[0]} in post {$event->post->id}");

            // Don't notify if user tagged themselves
            if (User::where('username', ltrim($match[0], '@'))
                    ->first()
                    ->value('id')
                === User::find($event->post->author)
                    ->value('id')
            ) continue;

            // Get User FCM Token
            $fcm_to = User::where('username', ltrim($match[0], '@'))
                ->first()
                ->value('fcm_token');

            // Create Notification
            $notification = (new PayloadNotificationBuilder())
                ->setTitle('New Tag')
                ->setBody("{$username} tagged you in a post")
                ->build();

            // Send Notification
            $response = FCM::sendToGroup($fcm_to, null, $notification, null);
        }
    }

    /**
     * Check comment text for user mention (@user)
     * Regex check: \B(\@[a-zA-Z\-\_]+\b)
     * Foreach result
     *   user = User::where('username', $result);
     * Create notification
     * Push notification
     *
     * @param $event
     * @return void
     */
    public function handleComment($event)
    {
        // DEBUG: Log event
        Log::notice($event->comment);

        // Scan Comment Text
        preg_match('/\B(\@[0-9a-zA-Z\-\_]+\b)/', $event->comment->text, $matches);

        // Get username of OP
        $username = User::find($event->comment->author)
            ->value('username');

        // Notify each match of tag
        foreach ($matches as $match) {
            Log::notice("Matched user tag {$match[0]} in comment {$event->comment->id}");

            // Don't notify if user tagged themselves
            if (User::where('username', ltrim($match[0], '@'))
                    ->first()
                    ->value('id')
                === User::find($event->comment->author)
                    ->value('id')
            ) continue;

            // Get User FCM Token
            $fcm_to = User::where('username', ltrim($match[0], '@'))
                ->first()
                ->value('fcm_token');

            // Create Notification
            $notification = (new PayloadNotificationBuilder())
                ->setTitle('New Tag')
                ->setBody("{$username} tagged you in a comment")
                ->build();

            // Send Notification
            $response = FCM::sendToGroup($fcm_to, null, $notification, null);
        }
    }

    /**
     * Handle event subscriptions
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events) {
        $events->listen(
            'App\Events\NewPost',
            'App\Listeners\CheckForUserMentions@handlePost'
        );

        $events->listen(
            'App\Events\NewPostRepost',
            'App\Listeners\CheckForUserMentions@handlePost'
        );

        $events->listen(
            'App\Events\NewComment',
            'App\Listeners\CheckForUserMentions@handleComment'
        );
    }
}
