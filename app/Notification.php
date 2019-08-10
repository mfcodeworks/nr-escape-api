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
        'type'
    ];
}
