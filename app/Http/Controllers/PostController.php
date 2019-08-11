<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Validator;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // Validate post
        $validator = Validator::make($request->all(), [
            'author' => 'required|exists:users,id',
            'type' => 'required|string',
            'media' => 'string',
            'caption' => 'string',
            'repost' => 'required|boolean'
        ], [
            'author.exists' => 'Author not found'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Unable to create new account, check your details',
                'validator' => $validator->errors()
            ], 401);
        }
        if (!$request->caption && !$request->media && !$request->repost) {
            return response()->json([
                'error' => 'Post cannot be empty, we need a little text or something'
            ]);
        }
        $media = $this->handleMedia($request->media);
        if (!$media) {
            return response()->json([
                'error' => 'It looks like the media for this post isn\'t anything we recognize'
            ], 400);
        }

        // Create new post
        $post = Post::create([
            'author' => $request->author,
            'type' => $request->type,
            'caption' => $request->caption,
            'media' => $media, // requires validation (may be image, video, or url)
            'repost' => $request->repost
        ]);

        if ($post) {
            return response()->json($post, 200);
        } else {
            return response()->json([
                'error' => 'Failed to create post'
            ], 500);
        }
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
        $post = auth()->user()->posts()->find($id);

        // If no post, post doesn't exist or isn't owned by user
        if (!$post) return $this->unauthorized();

        // if post updated response success, else response with error
        if ( $post->fill($request->all())->save() ) {
            return response()->json('success', 200);
        } else {
            return response()->json([
                'error' => 'Post could not be updated'
            ], 500);
        }
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
        if (!$post) return $this->unauthorized();

        // if post deleted response success, else response with error
        if ($post->delete()) {
            return response()->json('success', 200);
        } else {
            return response()->json([
                'error' => 'Post could not be deleted'
            ], 500);
        }
    }

    // Validate the post media type
    private function handleMedia($media) {
        // Check if media is a valid base64 string
        base64_decode($media, true) === false ? $isBase64 = true : $isBase64 = false;
        // Check if media is a valid URL
        filter_var($media, FILTER_VALIDATE_URL) ? $isUrl = true : $isUrl = false;

        switch(true) {
            case $isUrl:
                return $media;
                break;

            case $isBase64:
                
                break;

            default:
                return false;
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'Post doesn\'t exist or not owned by user'
        ], 400);
    }
}
