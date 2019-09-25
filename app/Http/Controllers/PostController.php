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
     * Instantiate a new UserController instance.
     */
   public function __construct() {
       // Check if blocked
       $this->middleware('blocked', [
           'only' => 'show'
       ]);
   }

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

        // Check if no caption, media, or repost
        if (!$request->caption && !isset($request->media) && !$request->repost) {
            return response()->json([
                'error' => 'Post cannot be empty, we need a little text or something'
            ]);
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
        $post->repost ? event(new NewPostRepost($post)) : event(new NewPost($post));

        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Select post by ID
        return response()->json(Post::findOrFail($id));
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
        $post = auth()->user()->posts()->find($id);

        // If no post, post doesn't exist or isn't owned by user
        if (!$post) {
            return $this->unauthorized();
        }

        $post->fill($request->all())->save();
        return response()->json($post::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // Get post from posts by user
        $post = auth()->user()->posts()->find($id);

        // If no post, post doesn't exist or isn't owned by user
        if (!$post) return $this->unauthorized();
        else {
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
