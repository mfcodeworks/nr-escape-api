<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // TODO: Set Rememberable
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'bio',
        'contact_info',
        'settings',
        'fcm_token',
        'deactivated'
    ];
    protected $attributes = [
        'profile_pic' => 'https://glamsquad.sgp1.cdn.digitaloceanspaces.com/SocialHub/default/images/profile.svg',
        'deactivated' => 0,
        'settings' => '{"private_account": false, "unknown_devices": true, "display_likes": true}'
    ];

    /**
     * The related objects that should be included
     *
     * @var array
     */
    protected $with = [
        'following',
        'followers'
    ];

    /**
     * The related object count that should be included
     *
     * @var array
     */
    protected $withCount = [
        'posts',
        'followers',
        'following'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'deactivated',
        'banned_until',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at',
        'fcm_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'banned_until' => 'datetime',
        'settings' => 'array',
        'contact_info' => 'array'
    ];

    // Return notifications for this user
    public function notifications() {
        return $this->hasMany('App\Notification', 'for_author');
    }

    // Return posts by this user
    public function posts() {
        return $this->hasMany('App\Post', 'author');
    }

    // Return recent posts by user
    public function recentPosts() {
        return $this->hasMany('App\Post', 'author')
            ->orderBy('updated_at', 'desc')
            ->limit(env('USER_POST_LIMIT', 15));
    }

    // Return comments by this user
    public function comments() {
        return $this->hasMany('App\Comment', 'author');
    }

    // Return likes by this user
    public function likes() {
        return $this->hasMany('App\Like', 'user');
    }

    // Return this users following
    public function following() {
        return $this->hasMany('App\Following', 'user');
    }

    // Return this users following requests
    public function followingRequest() {
        return $this->hasMany('App\FollowingRequest', 'following_user');
    }

    // Return this users followers
    public function followers() {
        return $this->hasMany('App\Following', 'following_user');
    }

    // Return the reports this user has made
    public function profileReports() {
        return $this->hasMany('App\ProfileReport', 'author');
    }
    public function postReports() {
        return $this->hasMany('App\PostReport', 'author');
    }

    // Return the reports against this user
    public function reportsAgainst() {
        return $this->hasMany('App\ProfileReport', 'reported_user');
    }

    // Return blocks by this user
    public function blocks() {
        return $this->hasMany('App\Block', 'user');
    }

    // Return block of this user
    public function blockedBy() {
        return $this->hasMany('App\Block', 'blocked_user');
    }
}
