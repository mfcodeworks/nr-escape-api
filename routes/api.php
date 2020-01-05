<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API: v1 routes
Route::prefix('v1')->group(function () {
    // login/signup no auth routes
    Route::post('signup', 'AuthController@register')
        ->name('user.store');

    Route::post('login', 'AuthController@login')
        ->name('user.login');

    Route::post('forgot', 'Auth\ForgotPasswordController@forgot')
        ->name('user.forgot');

    Route::post('reset', 'Auth\ResetPasswordController@reset')
        ->name('user.reset');

    // authorised routes
    Route::middleware(['auth:api', 'user.status'])->group(function () {
        // user routes
        Route::prefix('me')->group(function () {
            Route::get('/', 'AuthController@user')
                ->middleware('scope:basic-info,full-info')
                ->name('user.show');

            Route::post('fcm/token', 'FcmController@token')
                ->middleware('scopes:update-profile,update-profile-fcm')
                ->name('user.fcm.token');

            Route::post('fcm/subscribe/{topic}', 'FcmController@subscribe')
                ->middleware('scopes:update-profile,update-profile-fcm')
                ->name('user.fcm.subscribe');

            Route::post('fcm/unsubscribe/{topic}', 'FcmController@unsubscribe')
                ->middleware('scopes:update-profile,update-profile-fcm')
                ->name('user.fcm.unsubscribe');

            Route::put('update', 'AuthController@update')
                ->middleware('scopes:update-profile')
                ->name('user.update');

            Route::post('deactivate', 'AuthController@deactivate')
                ->middleware('scopes:full-info,update-profile')
                ->name('user.destroy');

            Route::get('notifications', 'NotificationController@index')
                ->middleware('scope:view-notifications')
                ->name('user.notifications');

            Route::get('engagement', 'EngagementScoreController')
                ->middleware('scope:full-info')
                ->name('user.engagement');

            Route::get('recommendations', 'RecommendationsController')
                ->middleware('scope:feed')
                ->name('user.recommendations');

            Route::get('feed', 'FeedController')
                ->middleware('scope:feed')
                ->name('user.feed');

            Route::get('blocked', 'BlockController@blocks')
                ->middleware('scope:full-info')
                ->name('user.blocked');

            Route::prefix('follower')->group(function () {
                Route::get('requests', 'FollowingRequestController@requests')
                    ->middleware('scope:full-info')
                    ->name('user.follower.requests');

                Route::post('approve/{id}', 'FollowingRequestController@approve')
                    ->middleware('scopes:full-info,update-profile')
                    ->name('user.follower.approve');

                Route::post('decline/{id}', 'FollowingRequestController@decline')
                    ->middleware('scopes:full-info,update-profile')
                    ->name('user.follower.decline');
            });
        });

        // resource routes
        Route::get('search', 'SearchController')
            ->middleware('throttle:40,1')
            ->name('profile.search');

        Route::get('profile/{username}', 'ProfileController@showUsername')
            ->name('profile.show.username');

        Route::apiResource('profile', 'ProfileController')
            ->only(['show']);

        Route::get('profile/{username}/posts', 'ProfileController@posts')
            ->name('profile.posts');

        Route::get('profile/{id}/requested', 'FollowingRequestController@requested')
            ->name('profile.follow.requested');

        Route::post('profile/{id}/block', 'BlockController@block')
            ->middleware('scope:block-accounts,')
            ->name('profile.block');

        Route::post('profile/{id}/unblock', 'BlockController@unblock')
            ->middleware('scope:block-accounts')
            ->name('profile.unblock');

        Route::post('profile/{id}/follow', 'FollowController@follow')
            ->middleware('scope:follow-accounts')
            ->name('profile.follow');

        Route::post('profile/{id}/unfollow', 'FollowController@unfollow')
            ->middleware('scope:follow-accounts')
            ->name('profile.unfollow');

        Route::post('profile/{id}/report', 'ReportController@store')
            ->middleware('scopes:full-info,update-profile')
            ->name('profile.report');

        Route::post('post/{id}/like', 'LikesController@store')
            ->middleware('scope:like-posts')
            ->name('post.like');

        Route::delete('post/{id}/like', 'LikesController@destroy')
            ->middleware('scope:like-posts')
            ->name('post.unlike');

        Route::post('post/{id}/report', 'ReportController@store')
            ->middleware('scopes:full-info,update-profile')
            ->name('post.report');

        Route::apiResource('notification', 'NotificationController')
            ->middleware('scope:view-notifications')
            ->only(['index', 'show']);

        Route::apiResource('post', 'PostController')
            ->middleware('scope:create-posts')
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('post', 'PostController')
            ->only(['show']);

        Route::apiResource('comment', 'CommentController')
            ->middleware('scope:create-comments')
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('comment', 'CommentController')
            ->only(['show']);
    });
});
