<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * Assignable notification values
     *
     * @var array
     */
    protected $fillable = [
        'for_author',
        'from_user',
        'post_id',
        'comment_id',
        'type'
    ];

    /**
     * The related objects that should be included
     *
     * @var array
     */
    protected $with = [
        'for',
        'from'
    ];

    public function for() {
        return $this->belongsTo('App\User', 'for_author');
    }

    public function from() {
        return $this->belongsTo('App\User', 'from_user');
    }

    public function post() {
        return $this->belongsTo('App\Post', 'post_id');
    }

    public function comment() {
        return $this->belongsTo('App\Comment', 'comment_id');
    }
}
