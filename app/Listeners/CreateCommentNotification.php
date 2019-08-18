<?php

namespace App\Listeners;

use App\Events\NewPostComment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateCommentNotification
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
     * @param  NewPostComment  $event
     * @return void
     */
    public function handle(NewPostComment $event)
    {
        //
    }
}
