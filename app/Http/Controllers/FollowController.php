<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Following;
use App\FollowingRequest;
use App\Events\NewFollower;
use Validator;

class FollowController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function follow(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (auth()->user()->can('follow', $user)) {
            // Create profile follow or request depending on users privacy setting
            $follow = $user->settings['private_account']
                ? FollowingRequest::create([
                    'following_user' => $id,
                    'user' => auth()->user()->id
                ]) : Following::create([
                    'following_user' => $id,
                    'user' => auth()->user()->id
                ]);

            event(new NewFollower($follow));

            return response()->json(
                $follow instanceof FollowingRequest
                    ? ['status' => 'requested']
                    : $follow
            );
        }

        // Check if user already following
        if (auth()->user()
            ->following
            ->where('following_user', $id)
            ->first()
        ) {
            return response()->json([
                'error' => 'User already followed'
            ], 403);

        // Check if request already made
        } else if (
            auth()->user()
                ->followingRequest
                ->where('following_user', $id)
                ->first()
        ) {
            return response()->json([
                'error' => 'Follow already requested'
            ], 403);

        // Check if user has blocked, or been blocked, by profile
        } else if (auth()->user()->blockingUser($id)) {
            return response()->json([
                'error' => 'Profile has been blocked, or blocked you'
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function unfollow($id)
    {
        if (auth()->user()->can('update', auth()->user())) {
            // Validate follow info and get follow
            $follow = auth()->user()
                ->following
                ->where('following_user', $id)
                ->first();

            // If no follow, follow doesn't exist
            if (!$follow) {
                return $this->unauthorized();
            }
            
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
