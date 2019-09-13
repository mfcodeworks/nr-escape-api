<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Events\NewComment;
use Illuminate\Http\Request;
use Validator;

class CommentController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // Validate comment
        $validator = Validator::make($request->all(), [
            'author' => 'required|exists:users,id',
            'text' => 'string|nullable',
            'media' => 'string|nullable',
            'reply_to' => 'required|integer'
        ], [
            'author.exists' => 'Author not found'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => 'Unable to create new comment',
                'validator' => $validator->errors()
            ], 401);
        }
        if (!$request->text && !$request->media) {
            return response()->json([
                'error' => 'Comment cannot be empty, we need a little text or something'
            ]);
        }

        // If media is present, handle the media (validate URL or upload image/video)
        if ($request->media) {
            $request->media = $this->handleMedia($request->media);
            if (!$request->media) {
                return response()->json([
                    'error' => 'It looks like the media for this post isn\'t anything we recognize'
                ], 400);
            }
        }

        // Create new comment
        $comment = Comment::create($request->all());

        // Send new comment event
        event(new NewComment($comment));

        return response()->json($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Select comment by ID
        return response()->json(Comment::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // Update comment by ID
        $comment = auth()->user()->comments()->find($id);

        // If no comment, comment doesn't exist or isn't owned by user
        if (!$comment) {
            return $this->unauthorized();
        }

        $comment->fill($request->all())->save();
        return response()->json(Comment::find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // Delete comment by ID
        $comment = auth()->user()->comments()->find($id);

        // If no comment, comment doesn't exist or isn't owned by user
        if (!$comment) {
            return $this->unauthorized();
        }

        $comment->delete();
        return response()->json('success', 204);
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
            $mediaPath = "SocialHub/author/{$id}/comments/$filename";

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
            'error' => 'Comment doesn\'t exist or not owned by user'
        ], 401);
    }
}
