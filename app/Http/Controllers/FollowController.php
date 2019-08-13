<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Following;
use Validator;

class FollowController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function follow(Request $request, $id)
    {
        // Validate follow info
        $validator = Validator::make($request->all(), [
            'user' => 'required|exists:users,id',
            'following_user' => 'required|exists:users,id'
        ]);
        if ($validator->fails() || auth()->user()->id !== $request->user || $request->following_user !== intval($id)) {
            return response()->json([
                'error' => 'Unable to follow user',
                'validator' => $validator->errors()
            ], 400);
        }
        if (auth()->user()
            ->following
            ->where('following_user', $id)
            ->first()
        ) {
            return response()->json([
                'error' => 'User already followed'
            ], 400);
        }

        // Create profile follow
        $follow = Following::create([
            'following_user' => $request->following_user,
            'user' => $request->user
        ]);

        // Return follow creation response
        if ($follow) {
            return response()->json($follow, 201);
        } else {
            return response()->json([
                'error' => 'Failed to follow user'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unfollow($id)
    {
        // Validate follow info and get follow
        $follow = auth()->user()
            ->following
            ->where('following_user', $id)
            ->first();

        // If no follow, follow doesn't exist
        if (!$follow) return $this->unauthorized();

        // if follow deleted response success, else response with error
        if ($follow->delete()) {
            return response()->json('success', 204);
        } else {
            return response()->json([
                'error' => 'User could not be unfollowed'
            ], 500);
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'User not being followed'
        ], 401);
    }
}
