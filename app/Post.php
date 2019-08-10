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
}
