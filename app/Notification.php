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

    public function for() {
        return $this->belongsTo('App\User', 'for_author');
    }

    public function from() {
        return $this->belongsTo('App\User', 'from_user');
    }
}
