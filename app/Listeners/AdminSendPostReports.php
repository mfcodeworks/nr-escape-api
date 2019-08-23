<?php

namespace App\Listeners;

use App\Events\PostReported;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class AdminSendPostReports
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
     * @param  PostReported  $event
     * @return void
     */
    public function handle(PostReported $event)
    {
        // Count recent reports
        $count = PostReport::where('created_at', '>', Carbon::now()->subMonths(env('PROFILE_REPORTS_PERIOD', 3))
            ->where('id', $event->PostReport->reported_post)
            ->count());

        if ($count > 3) {
            // TODO: Email Admins
            // Post {$event->PostReport->reported_post} has been reported {$count} times in the past {env('PROFILE_REPORTS_PERIOD', 3)} months
        }
    }
}
