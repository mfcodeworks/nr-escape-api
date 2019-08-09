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
    Route::post('signup', 'AuthController@register');
    Route::post('login', 'AuthController@login');

    // authorised user routes
    Route::middleware('auth:api')->group(function() {
        Route::get('me', 'AuthController@user');
        Route::get('me/engagement', 'EngagementScoreController@get');
        Route::get('recommendations', 'RecommendationsController@get');

        Route::apiResources([
            'profile' => 'ProfileController',
            'post' => 'PostController',
            'comment' => 'CommentController',
            'notification' => 'NotificationController'
        ]);
    });
});
