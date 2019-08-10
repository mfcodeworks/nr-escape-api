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
}
