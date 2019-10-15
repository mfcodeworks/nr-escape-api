<?php

namespace App\Http\Controllers;

use App\User;
use App\Post;
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Select user by ID
        return response()->json(User::findOrFail($id));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showUsername($username) {
        // Select user by ID
        return response()->json(User::where('username', '=', $username)->first());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function posts(Request $request, $id) {
        $posts = Post::where('author', '=', $id)
            ->latest()
            ->limit(15);

        if ($request->offset) {
            $posts = $posts->where('id', '<', $request->offset);
        }

        // Select posts by user ID
        return response()->json($posts->get());
    }
}
