<?php

namespace App\Http\Controllers;

use App\Like;
use App\Post;
use App\Events\NewPostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class LikesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        // Check if post already liked
        if (auth()->user()
            ->likes
            ->where('post', $id)
            ->first()
        ) {
            return response()->json([
                'error' => 'Post already liked'
            ], 400);
        }

        // Create post like
        if (auth()->user()->can('view', Post::findOrFail($id))) {
            $like = Like::create([
                'post' => $id,
                'user' => auth()->user()->id
            ]);

            event(new NewPostLike($like));

            return response()->json($like);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // TODO: Delete notification on like delete
        
        // Validate like info and get like
        $like = auth()->user()
            ->likes
            ->where('post', $id)
            ->first();

        // If no like, like doesn't exist or isn't owned by user
        if (auth()->user()->can('view', Post::findOrFail($id))) {
            if (!$like) {
                return $this->unauthorized();
            } else {
                $like->delete();
                return response()->json('success', 204);
            }
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'Like doesn\'t exist or not owned by user'
        ], 401);
    }
}
