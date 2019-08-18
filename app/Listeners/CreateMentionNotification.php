<?php

namespace App\Listeners;

use App\Events\UserMention;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateMentionNotification
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
     * @param  UserMention  $event
     * @return void
     */
    public function handle(UserMention $event)
    {
        //
    }
}
