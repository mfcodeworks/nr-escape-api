<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    /**
     * Assignable like values
     *
     * @var array
     */
    protected $fillable = [
        'post',
        'user'
    ];
}
