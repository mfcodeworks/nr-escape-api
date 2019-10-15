<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Following;
use App\Events\NewFollower;
use Validator;

class FollowController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * TODO: If account is private, create request not active follower
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function follow(Request $request, $id)
    {
        // Check if user already following
        if (auth()->user()
            ->following
            ->where('following_user', $id)
            ->first()
        ) {
            return response()->json([
                'error' => 'User already followed'
            ], 403);

        // Check if user has blocked, or been blocked, by profile
        } else if (
            auth()->user()->blocks->where('blocked_user', $id)->first() ||
            User::findOrFail($id)->blocks->where('blocked_user', auth()->user()->id)->first()
        ) {
            return response()->json([
                'error' => 'Profile has been blocked, or blocked you'
            ], 403);
        }

        // Create profile follow
        $follow = Following::create([
            'following_user' => $id,
            'user' => auth()->user()->id
        ]);

        event(new NewFollower($follow));

        return response()->json($follow);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
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
        if (!$follow) {
            return $this->unauthorized();
        } else {
            $follow->delete();
            return response()->json('success', 204);
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'User not being followed'
        ], 401);
    }
}
