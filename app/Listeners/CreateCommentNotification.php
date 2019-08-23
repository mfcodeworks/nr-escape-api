<?php

namespace App\Listeners;

use App\Post;
use App\Notification;
use App\Events\NewComment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateCommentNotification
{
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
     * @param  NewComment  $event
     * @return void
     */
    public function handle(NewComment $event)
    {
        // Get related users
        $to = Post::find($event->comment->reply_to)
            ->author()
            ->value('id');
        $from = $event->comment->author;

        // Create notification
        Notification::create([
            'for_author' => $to,
            'from_user' => $from,
            'comment_id' => $event->comment->id,
            'post_id' => $event->comment->reply_to,
            'type' => 'commented on'
        ]);
    }
}
