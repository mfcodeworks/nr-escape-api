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
    ];

    public function author() {
        return $this->belongsTo('App\User', 'author');
    }

    public function comments() {
        return $this->hasMany('App\Comment', 'reply_to');
    }

    public function likes() {
        return $this->hasMany('App\Like', 'post');
    }
}
