<?php

namespace App\Events;

use App\Post;
use Illuminate\Queue\SerializesModels;

class NewPostRepost
{
    use SerializesModels;

    public $post;

    /**
     * Create a new event instance.
     *
     * @param App\Post $post
     * @return void
     */
    public function __construct()
    {
        $this->post = $post;
    }
}
