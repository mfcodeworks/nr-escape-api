<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UserSignin
{
    use SerializesModels;

    public $request;

    /**
     * Create a new event instance.
     *
     * @param $request
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }
}
