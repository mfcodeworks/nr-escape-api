<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Validator;
use Storage;

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
            'media' => 'string|nullable',
            'caption' => 'string|nullable',
            'repost' => 'required|boolean',
            'repost_of' => 'integer|nullable'
        ], [
            'author.exists' => 'Author not found'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Unable to create new post',
                'validator' => $validator->errors()
            ], 400);
        }
        if (!$request->caption && !$request->media && !$request->repost) {
            return response()->json([
                'error' => 'Post cannot be empty, we need a little text or something'
            ]);
        }

        // If media is present, handle the media (either URL or image/video)
        if ($request->media) {
            $request->media = $this->handleMedia($request->media);
            if (!$request->media) {
                return response()->json([
                    'error' => 'It looks like the media for this post isn\'t anything we recognize'
                ], 400);
            }
        }

        // Create new post
        $post = Post::create([
            'author' => $request->author,
            'type' => $request->type,
            'caption' => $request->caption,
            'media' => $request->media, // requires validation (may be image, video, or url)
            'repost' => $request->repost,
            'repost_of' => $request->repost_of
        ]);

        // Return post creation response
        if ($post) {
            return response()->json($post, 201);
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
        // Update post by ID
        $post = auth()->user()->posts()->find($id);

        // If no post, post doesn't exist or isn't owned by user
        if (!$post) return $this->unauthorized();

        // if post updated response success, else response with error
        if ( $post->fill($request->all())->save() ) {
            return response()->json($post, 201);
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
            return response()->json('success', 204);
        } else {
            return response()->json([
                'error' => 'Post could not be deleted'
            ], 500);
        }
    }

    // Validate the post media type
    private function handleMedia($media) {
        // Check if media is a valid base64
        if (strpos($media, 'base64,') !== false || base64_decode($media, true) !== false) {
            // Get file data
            $fileData = explode(';base64,', $media);
            // After ;base64, is the actual base64 string
            $base64 = $fileData[1];
            // data:{generic_type}/{extension} get extension after the /
            $extension = explode('/', $fileData[0])[1];

            // Create filepath
            $filename = uniqid() . "." . $extension;
            $id = auth()->user()->id;
            $mediaPath = "SocialHub/author/{$id}/posts/$filename";

            // Save media to DigitalOcean Spaces
            Storage::disk('spaces')->put($mediaPath, base64_decode($base64), 'public');
            $media = Storage::disk('spaces')->url($mediaPath);
        }

        // Check if media is a valid URL and return URL or false
        if (filter_var($media, FILTER_VALIDATE_URL)) {
            return $media;
        } else {
            return false;
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'Post doesn\'t exist or not owned by user'
        ], 401);
    }
}
