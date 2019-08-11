<?php

namespace App\Http\Controllers;

use App\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class LikesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        // Validate like info
        $validator = Validator::make($request->all(), [
            'post' => 'required|exists:posts,id',
            'user' => 'required|exists:users,id'
        ]);
        if ($validator->fails() || auth()->user()->id !== $request->user || $request->post !== intval($id)) {
            return response()->json([
                'error' => 'Unable to like post',
                'validator' => $validator->errors()
            ], 400);
        }
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
        $like = Like::create([
            'post' => $request->post,
            'user' => $request->user
        ]);

        // Return like creation response
        if ($like) {
            return response()->json($like, 201);
        } else {
            return response()->json([
                'error' => 'Failed to like post'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Validate like info and get like
        $like = auth()->user()
            ->likes
            ->where('post', $id)
            ->first();

        // If no like, like doesn't exist or isn't owned by user
        if (!$like) return $this->unauthorized();

        // if like deleted response success, else response with error
        if ($like->delete()) {
            return response()->json('success', 204);
        } else {
            return response()->json([
                'error' => 'Like could not be removed'
            ], 500);
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'Like doesn\'t exist or not owned by user'
        ], 401);
    }
}
