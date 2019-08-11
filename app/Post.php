<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Assignable post values
     *
     * @var array
     */
    protected $fillable = [
        'author',
        'type',
        'media',
        'caption',
        'repost',
        'repost_of'
    ];

    /**
     * The related objects that should be included
     *
     * @var array
     */
    protected $with = [
        'author',
        'recentComments',
        'likes',
        'reposts',
        'repostOf'
    ];

    /**
     * The related object count that should be included
     *
     * @var array
     */
    protected $withCount = [
        'likes',
        'comments',
        //'reposts' DEBUG: cannot count reposts
    ];

    public function author() {
        return $this->belongsTo('App\User', 'author')->without('recentPosts');
    }

    public function comments() {
        return $this->hasMany('App\Comment', 'reply_to');
    }

    // Return recent comments on post
    public function recentComments() {
        return $this->hasMany('App\Comment', 'reply_to')
            ->orderBy('updated_at', 'desc')
            ->limit(env('POST_COMMENT_LIMIT', 15));
    }

    public function likes() {
        return $this->hasMany('App\Like', 'post');
    }

    public function reposts() {
        return $this->hasMany(self::class, 'repost_of')
            ->where('repost', 1)
            ->without('recentComments', 'reposts');
    }

    public function repostOf() {
        return $this->belongsTo(self::class, 'repost')
            ->without('recentComments', 'reposts');
    }
}
