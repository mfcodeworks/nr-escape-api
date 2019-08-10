<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    /**
     * Assignable following values
     *
     * @var array
     */
    protected $fillable = [
        'user',
        'following_user'
    ];
}
