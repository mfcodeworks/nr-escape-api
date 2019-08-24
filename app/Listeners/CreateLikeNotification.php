<?php

namespace App\Listeners;

use App\Post;
use App\Notification;
use App\Events\NewPostLike;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateLikeNotification implements ShouldQueue
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
     * @param  NewPostLike  $event
     * @return void
     */
    public function handle(NewPostLike $event)
    {
        // Get related users
        $to = Post::find($event->like->post)
            ->author()
            ->value('id');
        $from = $event->like->user;

        // Create notification
        Notification::create([
            'for_author' => $to,
            'from_user' => $from,
            'post_id' => $event->like->post,
            'type' => 'liked'
        ]);
    }
}
