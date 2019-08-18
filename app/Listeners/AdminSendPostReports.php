<?php

namespace App\Listeners;

use App\Events\PostReported;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminSendPostReports
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
     * @param  PostReported  $event
     * @return void
     */
    public function handle(PostReported $event)
    {
        //
    }
}
