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

        /**
         * Check user signin device against known devices
         *
         * For user check if:
         * - Device and IP (Nexus at 102.66.226.55)
         * - Device and platform and browser (Nexus Android 4.0 Chrome)
         *
         */
        if (!$agent['robot']) {
            // Check if any similar device
            Device::where('user_id', $agent['user_id'])
                ->where('device', $agent['device'])
                ->where(function($q) use ($agent) {
                    $q->where('ip', $agent['ip'])
                        ->orWhere(function($r) use ($agent) {
                            $r->where('platform', $agent['platform'])
                                ->where('browser', $agent['browser']);
                        });
                })
                ->first();

            // Send unknown device email
            $user = User::find($agent['user_id']);
            $beautymail = app()->make('Snowfire\Beautymail\Beautymail');
            $beautymail->send('emails.unknown-device', ['agent' => $agent], function($message) use ($user) {
                $message
                    ->from('it@nygmarosebeauty.com')
                    ->to($user->email, $user->username)
                    ->subject('Escape unknown device login');
            });

            // Save device
            Device::create($agent);
        }
    }
}
