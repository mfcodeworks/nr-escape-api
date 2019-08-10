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

    // authorised routes
    Route::middleware('auth:api')->group(function() {
        // user routes
        Route::get('me', 'AuthController@user')->name('user.show');
        Route::put('me/update', 'AuthController@update')->name('user.update');
        Route::post('me/deactivate', 'AuthController@deactivate')->name('user.destroy');
        Route::get('me/engagement', 'EngagementScoreController@get')->name('user.engagement');
        Route::get('me/recommendations', 'RecommendationsController@get')->name('user.recommendations');

        // resource routes
        Route::apiResource('profile', 'Profilecontroller')->only(['show']);
        Route::apiResource('post', 'PostController')->only(['store', 'show', 'update', 'destroy']);
        Route::apiResource('comment', 'CommentController')->only(['store', 'show', 'update', 'destroy']);
        Route::apiResource('notification', 'NotificationController')->only(['store', 'show', 'update', 'destroy']);
    });
});
