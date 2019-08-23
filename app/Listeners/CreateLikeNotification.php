<?php

namespace App\Listeners;

use App\Events\NewPostLike;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateLikeNotification
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
            ->pluck('id');
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
