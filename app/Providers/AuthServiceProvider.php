<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensCan([
            'basic-info' => 'View basic account info',
            'full-info' => 'View all account info (includes settings, bio, and followers)',
            'update-profile' => 'Update profile info',
            'update-profile-fcm' => 'Update notification subscriptions',
            'feed' => 'View feed',
            'view-notifications' => 'View profile notifications',
            'view-posts' => 'View profile posts',
            'create-posts' => 'Create and delete posts',
            'follow-accounts' => 'Follow and unfollow accounts on your behalf',
            'block-accounts' => 'Block and unblock users on your behalf',
            'create-comments' => 'Create and delete comments on your behalf',
            'like-posts' => 'Like and unlike posts on your behalf'
        ]);

        Passport::setDefaultScope(['basic-info']);
    }
}
