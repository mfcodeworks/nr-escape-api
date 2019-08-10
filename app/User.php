<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
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
        'profile_pic',
        'bio',
        'contact_info',
        'settings'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Return notifications for this user
    public function notifications() {
        return $this->hasMany('App\Notification', 'for_author');
    }

    // Return posts by this user
    public function posts() {
        return $this->hasMany('App\Post', 'author');
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

    // Return this users followers
    public function followers() {
        return $this->hasMany('App\Following', 'following_user');
    }
}
