<?php

namespace App\Events;

use App\ProfileReport;
use Illuminate\Queue\SerializesModels;

class ProfileReported
{
    use SerializesModels;

    public $report;

    /**
     * Create a new event instance.
     *
     * @param App\ProfileReport $report;
     * @return void
     */
    public function __construct(ProfileReport $report)
    {
        $this->report = $report;
    }
}
