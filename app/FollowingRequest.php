<?php

namespace App;

use App\Following;

class FollowingRequest extends Following
{
    // Model table
    protected $table = 'following_requests';

    /**
     * Attributes to always return with object
     *
     * @var array
     */
    protected $with = [
        'author', // Parent belongsTo
        'followingUser'
    ];

    /**
     * Attributes, with, and related objects inherited from App\Following
     */

    // Model functions

    public function approve() {
        $follow = new Following;
        $follow->fill([
            'user' => $this->user,
            'following_user' => $this->following_user,
        ])->save();
        $this->delete();
        return $follow;
    }

    public function decline() {
        return $this->delete();
    }
}
