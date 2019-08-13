<?php

namespace App\Http\Controllers;

use App\User;
use App\Block;
use Illuminate\Http\Request;
use Validator;

class BlockController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function block(Request $request, $id)
    {
        // Validate block info
        $validator = Validator::make($request->all(), [
            'user' => 'required|exists:users,id',
            'blocked_user' => 'required|exists:users,id'
        ]);
        if ($validator->fails() || auth()->user()->id !== $request->user || $request->blocked_user !== intval($id)) {
            return response()->json([
                'error' => 'Unable to block user',
                'validator' => $validator->errors()
            ], 400);
        }
        if (auth()->user()
            ->blocks
            ->where('blocked_user', $id)
            ->first()
        ) {
            return response()->json([
                'error' => 'User already blocked'
            ], 400);
        }

        // Create profile block
        $block = Block::create([
            'blocked_user' => $request->blocked_user,
            'user' => $request->user
        ]);

        // If user was previously following profile, remove the follow
        auth()->user()
            ->following
            ->where('following_user', $id)
            ->first()
            ->delete();

        // Return block creation response
        if ($block) {
            return response()->json($block, 201);
        } else {
            return response()->json([
                'error' => 'Failed to block user'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unblock($id)
    {
        // Validate block info and get block
        $block = auth()->user()
            ->blocks
            ->where('blocked_user', $id)
            ->first();

        // If no block, block doesn't exist or isn't owned by user
        if (!$block) return $this->unauthorized();

        // if block deleted response success, else response with error
        if ($block->delete()) {
            return response()->json('success', 204);
        } else {
            return response()->json([
                'error' => 'Block could not be removed'
            ], 500);
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'Block doesn\'t exist or not owned by user'
        ], 401);
    }
}
