<?php

namespace App\Events;

use App\Comment;
use Illuminate\Queue\SerializesModels;

class NewComment
{
    use SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     *
     * @param App\Comment $comment
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }
}
