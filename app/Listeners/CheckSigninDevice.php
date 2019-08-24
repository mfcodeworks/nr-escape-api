<?php

namespace App\Listeners;

use App\User;
use App\Device;
use Jenssegers\Agent\Agent;
use App\Events\UserSignin;
use Illuminate\Support\Facades\Log;

class CheckSigninDevice
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
        // Reference agent
        $agent = $event->request;

        // Log Request
        Log::info($agent);

        // TODO: Check user signin device against known devices
        if (!$agent['robot']) {
            // Save device
            Device::create($agent);
        }
    }
}
