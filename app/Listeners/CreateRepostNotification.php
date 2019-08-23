<?php

namespace App\Listeners;

use App\Post;
use App\Notification;
use App\Events\NewPostRepost;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateRepostNotification
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
     * @param  NewPostRepost  $event
     * @return void
     */
    public function handle(NewPostRepost $event)
    {
        // Get related users
        $to = Post::find($event->post->repost_of)
            ->author()
            ->value('id');
        $from = $event->post->author;

        // Create notification
        Notification::create([
            'for_author' => $to,
            'from_user' => $from,
            'post_id' => $event->post->id,
            'type' => 'reposted'
        ]);
    }
}
