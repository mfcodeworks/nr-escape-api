<?php

namespace App\Listeners;

use App\Events\NewFollower;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushFollowerNotification
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
        //
    }
}
