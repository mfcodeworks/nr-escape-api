<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // TODO: Store new post
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Select post by ID
        return Post::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // TODO: Update post by ID
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // Get post from posts by user
        $post = auth()->user()->posts()->find($id);

        // If no post, post doesn't exist or isn't owned by user
        if (!$post) {
            return response()->json([
                'error' => 'Post doesn\'t exist or not owned by user'
            ], 400);
        }

        // if post deleted response success, else response with error
        if ($post->delete()) {
            return response()->json('success', 200);
        } else {
            return response()->json([
                'error' => 'Post could not be deleted'
            ], 500);
        }
    }
}
