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
     * The related objects that should be included
     *
     * @var array
     */

    public function user() {
        return $this->belongsTo('App\User', 'user')->without('recentPosts', 'following', 'followers');
    }

    public function followingUser() {
        return $this->belongsTo('App\User', 'following_user')->without('recentPosts', 'following', 'followers');
    }
}
