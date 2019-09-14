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
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function posts(Request $request, $id) {
        // Select posts by user ID
        return response()->json(
            Post::where('author', '=', $id)
                ->offset($request->offset | 0)
                ->limit(15)
                ->get()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        // Format query
        $query = $this->formatQuery($request->input('query'));

        // Select user by ID
        return response()->json(
            User::where('username', 'like', $query)
                ->limit(45)
                ->get()
        );
    }

    /**
     * Format search query
     *
     * @param string $query
     * @return string
     */
    private function formatQuery($query) {
        return '%'.str_replace(' ', '%', $query).'%';
    }
}
