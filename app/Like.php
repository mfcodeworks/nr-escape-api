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

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function post() {
        return $this->belongsTo('App\Post', 'post');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user');
    }
}
