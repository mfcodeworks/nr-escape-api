<?php

namespace App\Listeners;

use App\Events\NewPostLike;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateLikeNotification
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
     * @param  NewPostLike  $event
     * @return void
     */
    public function handle(NewPostLike $event)
    {
        //
    }
}
