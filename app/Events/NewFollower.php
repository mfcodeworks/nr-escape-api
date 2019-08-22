<?php

namespace App\Events;

use App\Following;
use Illuminate\Queue\SerializesModels;

class NewFollower
{
    use SerializesModels;

    public $following;

    /**
     * Create a new event instance.
     *
     * @param App\Following $following
     * @return void
     */
    public function __construct(Following $following)
    {
        $this->following = $following;
    }
}
