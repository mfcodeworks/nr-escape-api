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
    Route::middleware(['auth:api', 'user.status'])->group(function() {
        // user routes
        Route::prefix('me')->group(function() {
            Route::get('/', 'AuthController@user')->name('user.show');
            Route::put('update', 'AuthController@update')->name('user.update');
            Route::post('deactivate', 'AuthController@deactivate')->name('user.destroy');
            Route::get('notifications', 'NotificationController@index')->name('user.notifications');
            Route::get('engagement', 'EngagementScoreController')->name('user.engagement');
            Route::get('recommendations', 'RecommendationsController')->name('user.recommendations');
        });

        // resource routes
        Route::apiResource('profile', 'ProfileController')->only(['show'])->middleware('blocked');
        Route::post('profile/{id}/block', 'BlockController@block');
        Route::post('profile/{id}/unblock', 'BlockController@unblock');
        Route::apiResource('notification', 'NotificationController')->only(['index', 'show']);
        Route::apiResource('post', 'PostController')->only(['store', 'show', 'update', 'destroy'])->middleware('blocked');
        Route::post('post/{id}/like', 'LikesController@store');
        Route::delete('post/{id}/like', 'LikesController@destroy');
        Route::apiResource('comment', 'CommentController')->only(['store', 'show', 'update', 'destroy']);
    });
});
