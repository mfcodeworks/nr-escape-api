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
    Route::post('signup', 'AuthController@register')->name('user.store');
    Route::post('login', 'AuthController@login')->name('user.login');
    Route::post('forgot', 'Auth\ForgotPasswordController@forgot')->name('user.forgot');
    Route::post('reset', 'Auth\ResetPasswordController@reset')->name('user.reset');

    // authorised routes
    Route::middleware(['auth:api', 'user.status'])->group(function () {
        // user routes
        Route::prefix('me')->group(function () {
            Route::get('/', 'AuthController@user')->name('user.show');
            Route::post('fcm/token', 'FcmController@token')->name('user.fcm.token');
            Route::post('fcm/subscribe/{topic}', 'FcmController@subscribe')->name('user.fcm.subscribe');
            Route::post('fcm/unsubscribe/{topic}', 'FcmController@unsubscribe')->name('user.fcm.unsubscribe');
            Route::put('update', 'AuthController@update')->name('user.update');
            Route::post('deactivate', 'AuthController@deactivate')->name('user.destroy');
            Route::get('notifications', 'NotificationController@index')->name('user.notifications');
            Route::get('engagement', 'EngagementScoreController')->name('user.engagement');
            Route::get('recommendations', 'RecommendationsController')->name('user.recommendations');
            Route::get('feed', 'FeedController')->name('user.feed');
            Route::get('blocked', 'BlockController@blocks')->name('user.blocked');
            Route::prefix('follower')->group(function () {
                Route::get('requests', 'FollowingRequestController@requests')->name('user.follower.requests');
                Route::post('approve/{id}', 'FollowingRequestController@approve')->name('user.follower.approve');
                Route::post('decline/{id}', 'FollowingRequestController@decline')->name('user.follower.decline');
            });
        });

        // resource routes
        Route::get('search', 'SearchController')->name('profile.search');
        Route::get('profile/{username}', 'ProfileController@showUsername')->name('profile.show.username');
        Route::apiResource('profile', 'ProfileController')->only(['show']);
        Route::get('profile/{id}/posts', 'ProfileController@posts')->name('profile.posts');
        Route::get('profile/{id}/requested', 'FollowingRequestController@requested')->name('profile.follow.requested');
        Route::post('profile/{id}/block', 'BlockController@block')->name('profile.block');
        Route::post('profile/{id}/unblock', 'BlockController@unblock')->name('profile.unblock');
        Route::post('profile/{id}/follow', 'FollowController@follow')->name('profile.follow');
        Route::post('profile/{id}/unfollow', 'FollowController@unfollow')->name('profile.unfollow');
        Route::post('profile/{id}/report', 'ReportController@store')->name('profile.report');
        Route::apiResource('notification', 'NotificationController')->only(['index', 'show']);
        Route::apiResource('post', 'PostController');
        Route::post('post/{id}/like', 'LikesController@store')->name('post.like');
        Route::delete('post/{id}/like', 'LikesController@destroy')->name('post.unlike');
        Route::post('post/{id}/report', 'ReportController@store')->name('post.report');
        Route::apiResource('comment', 'CommentController');
    });
});
