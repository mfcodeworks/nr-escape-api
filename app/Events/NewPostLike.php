<?php

namespace App\Events;

use App\Like;
use Illuminate\Queue\SerializesModels;

class NewPostLike
{
    use SerializesModels;

    public $like;

    /**
     * Create a new event instance.
     *
     * @param App\Like $like
     * @return void
     */
    public function __construct(Like $like)
    {
        $this->like = $like;
    }
}
