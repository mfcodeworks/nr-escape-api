<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    // Model table
    protected $table = 'following';

    /**
     * Assignable following values
     *
     * @var array
     */
    protected $fillable = [
        'user',
        'following_user'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];


    /**
     * The related objects that should be included
     *
     * @var array
     */

    public function author() {
        return $this->belongsTo('App\User', 'user')->without('recentPosts', 'following', 'followers');
    }

    public function followingUser() {
        return $this->belongsTo('App\User', 'following_user')->without('recentPosts', 'following', 'followers');
    }
}
