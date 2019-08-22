<?php

namespace App\Events;

use App\PostReport;
use Illuminate\Queue\SerializesModels;

class PostReported
{
    use SerializesModels;

    public $report;

    /**
     * Create a new event instance.
     *
     * @param App\PostReport $report
     * @return void
     */
    public function __construct(PostReport $report)
    {
        $this->report = $report;
    }
}
