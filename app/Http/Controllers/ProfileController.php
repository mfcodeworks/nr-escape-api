<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     */
   public function __construct()
   {
       // Check if blocked
       $this->middleware('blocked', [
           'only' => 'show'
       ]);
   }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Select user by ID
        return User::find($id);
    }
}
