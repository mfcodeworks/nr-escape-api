<?php

namespace App;

use App\Following;

class FollowingRequest extends Following
{
    // Model table
    protected $table = 'following_requests';

    /**
     * Attributes, with, and related objects inherited from App\Following
     */

    // Model functions

    public function approve() {
        $follow = Following::create($this->toArray());
        $this->delete();
        return $follow;
    }

    public function decline() {
        return $this->delete();
    }
}
