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

    public function user() {
        return $this->belongsTo('App\User', 'author');
    }

    public function post() {
        return $this->belongsTo('App\User', 'reply_to');
    }
}
