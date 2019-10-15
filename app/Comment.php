<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * Assignable comment values
     *
     * @var array
     */
    protected $fillable = [
        'author',
        'text',
        'media',
        'reply_to',
    ];

    /**
     * The related objects that should be included
     *
     * @var array
     */
    protected $with = [
        'author'
    ];

    public function author() {
        return $this->belongsTo('App\User', 'author');
    }

    public function post() {
        return $this->belongsTo('App\Post', 'reply_to')
            ->without('comments');
    }
}
