<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Comment extends Model
{
    use Rememberable;

    public $rememberCacheTag = 'comments';
    public $rememberFor = 3600;

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
        return $this->belongsTo('App\User', 'author')->without('recentPosts');
    }

    public function post() {
        return $this->belongsTo('App\Post', 'reply_to')->without('recentComments', 'author');
    }
}
