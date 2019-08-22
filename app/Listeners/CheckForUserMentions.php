<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckForUserMentions
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
     * TODO: Check post/comment text for user mention (@user)
     * Regex check: \B(\@[a-zA-Z\-\_]+\b)
     * Foreach result
     *   event(new UserMention(User::where('username', $result));
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
