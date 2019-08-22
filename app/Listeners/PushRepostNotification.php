<?php

namespace App\Listeners;

use App\Events\NewPostRepost;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushRepostNotification
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
     * @param  NewPostRepost  $event
     * @return void
     */
    public function handle(NewPostRepost $event)
    {
        //
    }
}
