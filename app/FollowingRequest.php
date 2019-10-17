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
        'author',
        'followingUser'
    ];

    /**
     * Attributes, with, and related objects inherited from App\Following
     */

    // Model functions

    public function approve() {
        $follow = Following::create([
            'user' => $this->user,
            'following_user' => $this->following_user,
        ]);
        FollowingRequest::delete($this->id);
        return $follow;
    }

    public function decline() {
        return FollowingRequest::delete($this->id);
    }
}
