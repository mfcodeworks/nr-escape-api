<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostReport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author',
        'reported_post',
    ];

    public function author() {
        return $this->belongsTo('App\User', 'author');
    }

    public function reportedPost() {
        return $this->belongsTo('App\Post', 'reported_post');
    }
}
