<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    // Model table
    protected $table = 'following';

    /**
     * Assignable following values
     *
     * @var array
     */
    protected $fillable = [
        'user',
        'following_user'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user');
    }

    public function following() {
        return $this->belongsTo('App\User', 'following_user');
    }
}
