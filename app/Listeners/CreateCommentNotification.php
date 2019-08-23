<?php

namespace App\Listeners;

use App\Notification;
use App\Events\NewPostComment;
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
     * @param  NewPostComment  $event
     * @return void
     */
    public function handle(NewPostComment $event)
    {
        // Get related users
        $to = Post::find($event->comment->reply_to)
            ->author()
            ->pluck('id');
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
