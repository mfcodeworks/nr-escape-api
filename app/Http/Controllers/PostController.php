<?php

namespace App\Http\Controllers;

use App\Post;
use App\Events\NewPost;
use App\Events\NewPostRepost;
use Illuminate\Http\Request;
use Validator;
use Storage;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // Validate post
        $validator = Validator::make($request->all(), [
            'author' => 'required|exists:users,id',
            'type' => 'required|string',
            'caption' => 'string|nullable',
            'repost' => 'required',
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

        // Parse repost from string to boolean
        $request->repost = (bool) $request->repost === 'true' ? true : false;

        // Check if no caption, media, or repost
        if (!$request->caption && !isset($request->media) && !$request->repost) {
            return response()->json([
                'error' => 'Post cannot be empty, we need a little text or something'
            ]);
        }

        if (auth()->user()->can('create', Post::class)) {
            if ($request->repost == true && $request->repost_of) {
                if (!auth()->user()->can('repost', Post::find($request->repost_of))) {
                    return response()->json([
                        'error' => 'Cannot repost this post'
                    ], 403);
                }
            }

            $postData = $request->all();

            // If media is present, handle the media (either URL or image/video)
            if ($request->hasFile('media')) {
                $user = auth()->user()->id;
                $path = Storage::disk('spaces')->put("SocialHub/author/{$user}/posts", $request->media, 'public');
                $postData['media'] = Storage::disk('spaces')->url($path);
            } else if ($request->media) {
                $postData['media'] = $this->handleMedia($request->media);
                if (!$postData['media']) {
                    return response()->json([
                        'error' => 'It looks like the media for this post isn\'t anything we recognize'
                    ], 400);
                }
            }

            // Create new post
            $post = Post::create($postData);

            // Dispatch event, either new post or repost
            $post->repost == true ? event(new NewPostRepost($post)) : event(new NewPost($post));

            return response()->json($post);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $post = Post::findOrFail($id);

        if (!auth()->user()->can('view_likes', $post)) {
            $post = $post->without('likes');
        }

        if (auth()->user()->can('view', $post)) {
            return response()->json($post);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // Update post by ID
        $post = auth()->user()->posts()->findOrFail($id);

        if (auth()->user()->can('update', $post)) {
            $post->fill($request->all())->save();
            return response()->json($post->refresh());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // TODO: On delete check removed comments and notifications

        // Get post from posts by user
        $post = auth()->user()->posts()->findOrFail($id);

        if (auth()->user()->can('delete', $post)) {
            $post->delete();
            return response()->json('success', 204);
        }
    }

    /**
     * Validate the comment media type
     *
     * @param string $media Base64 or URL
     * @return string media URL
     */
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
        return filter_var($media, FILTER_VALIDATE_URL) ? $media : false;
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'Post doesn\'t exist or not owned by user'
        ], 401);
    }
}
