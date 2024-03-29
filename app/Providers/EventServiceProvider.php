<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Define events and listeners
        'App\Events\UserSignin' => [
            'App\Listeners\CheckSigninDevice'
        ],
        'App\Events\NewPostLike' => [
            'App\Listeners\PushLikeNotification',
            'App\Listeners\CreateLikeNotification'
        ],
        'App\Events\NewPostRepost' => [
            'App\Listeners\PushRepostNotification',
            'App\Listeners\CreateRepostNotification'
        ],
        'App\Events\NewComment' => [
            'App\Listeners\PushCommentNotification',
            'App\Listeners\CreateCommentNotification'
        ],
        'App\Events\NewPost' => [
            'App\Listeners\PushNewPostNotifications'
        ],
        'App\Events\NewFollower' => [
            'App\Listeners\PushFollowerNotification',
            'App\Listeners\CreateFollowerNotification'
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'App\Listeners\CheckForUserMentions',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
