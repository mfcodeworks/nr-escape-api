<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    /**
     * Assignable like values
     *
     * @var array
     */
    protected $fillable = [
        'device',
        'browser',
        'ip',
        'platform',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
