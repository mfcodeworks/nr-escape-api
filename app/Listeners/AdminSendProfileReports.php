<?php

namespace App\Listeners;

use App\Events\ProfileReported;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminSendProfileReports
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
     * @param  ProfileReported  $event
     * @return void
     */
    public function handle(ProfileReported $event)
    {
        //
    }
}
