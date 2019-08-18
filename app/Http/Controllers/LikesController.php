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
        return Like::create([
            'post' => $id,
            'user' => auth()->user()->id
        ]);
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
        if (!$like) {
            return $this->unauthorized();
        } else {
            $like->delete();
            return response()->json('', 204);
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'Like doesn\'t exist or not owned by user'
        ], 401);
    }
}
