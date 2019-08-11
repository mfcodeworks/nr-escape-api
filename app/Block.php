<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    /**
     * Assignable like values
     *
     * @var array
     */
    protected $fillable = [
        'user',
        'blocked_user'
    ];

    public function blockedUser() {
        return $this->belongsTo('App\User', 'user');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user');
    }
}
