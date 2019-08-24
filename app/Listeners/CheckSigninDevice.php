<?php

namespace App\Listeners;

use App\Events\UserSignin;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckSigninDevice implements ShouldQueue
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
     * @param  UserSignin  $event
     * @return void
     */
    public function handle(UserSignin $event)
    {
        // TODO: Check user signin device against known devices
    }
}
