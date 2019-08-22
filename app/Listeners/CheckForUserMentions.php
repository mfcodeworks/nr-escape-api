<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckForUserMentions
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
     * TODO: Check post/comment text for user mention (@user)
     * Regex check: \B(\@[a-zA-Z\-\_]+\b)
     * Foreach result
     *   user = User::where('username', $result);
     * Create notification
     * Push notification
     *
     * @param $event
     * @return void
     */
    public function handle($event)
    {

    }

    /**
     * Handle event subscriptions
     *
     * @param $event
     * @return void
     */
    public function subscribe($events) {
        $events->listen(
            'App\Events\NewPost',
            'App\Listeners\CheckForUserMentions@handle'
        );

        $events->listen(
            'App\Events\NewComment',
            'App\Listeners\CheckForUserMentions@handle'
        );
    }
}
