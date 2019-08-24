<?php

namespace App\Listeners;

use App\ProfileReport;
use App\Events\ProfileReported;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class AdminSendProfileReports
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'email';

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
        // Count recent reports
        $count = ProfileReport::where('created_at', '>', Carbon::now()->subMonths(env('PROFILE_REPORTS_PERIOD', 3)))
            ->where('reported_user', $event->report->reported_user)
            ->count();

        if ($count > 3) {
            // TODO: Email Admins
            // Profile {$event->ProfileReport->reported_user} has been reported {$count} times in the past {env('PROFILE_REPORTS_PERIOD', 3)} months
        }
    }
}
