<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author',
        'reported_user'
    ];

    public function author() {
        return $this->belongsTo('App\User', 'author');
    }

    public function reportedUser() {
        return $this->belongsTo('App\User', 'reported_user');
    }
}
