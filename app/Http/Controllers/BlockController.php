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
        // Check for existing block
        if (auth()->user()
            ->blocks
            ->where('blocked_user', $id)
            ->first()
        ) {
            return response()->json([
                'error' => 'User already blocked'
            ], 400);
        }

        // If user was previously following profile, remove the follow
        if (auth()->user()
            ->following
            ->where('following_user', $id)
            ->first()
        ) {
            auth()->user()
                ->following
                ->where('following_user', $id)
                ->first()
                ->delete();
        }

        // Create profile block
        return Block::create([
            'blocked_user' => $id,
            'user' => auth()->user()->id
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unblock($id)
    {
        // Get block
        $block = auth()->user()
            ->blocks
            ->where('blocked_user', $id)
            ->first();

        // If no block, block doesn't exist or isn't owned by user
        if (!$block) {
            return $this->unauthorized();
        } else {
            $block->delete();
            return response()->json('', 204);
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'User not blocked'
        ], 401);
    }
}
