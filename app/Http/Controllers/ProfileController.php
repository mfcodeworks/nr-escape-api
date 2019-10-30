<?php

namespace App\Http\Controllers;

use App\User;
use App\Post;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = User::findOrFail($id);

        if (auth()->user()->can('view', $user)) {
            return response()->json($user);
        } else if (auth()->user()->can('view_restricted', $user)) {
            return response()->json(
                $user->without(
                    'contact_info',
                    'following',
                    'followers',
                    'posts_count',
                    'followers_count',
                    'following_count'
                )
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showUsername($username) {
        $user = User::where('username', $username)->first();

        if (auth()->user()->can('view', $user)) {
            return response()->json($user);
        } else if (auth()->user()->can('view_restricted', $user)) {
            return response()->json(
                $user->only(
                    'username',
                    'bio',
                    'settings',
                    'profile_pic'
                )
            );
        } else {
            return response()->json([
                'error' => 'Not authorized to view account'
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function posts(Request $request, $id) {
        $user = User::findOrFail($id);
        $posts = Post::where('author', $id)
            ->latest()
            ->limit(15);

        if (auth()->user()->can('view', $user)) {
            if ($request->offset) {
                $posts = $posts->where('id', '<', $request->offset);
            }

            // Select posts by user ID
            return response()->json($posts->get());
        }
    }
}
