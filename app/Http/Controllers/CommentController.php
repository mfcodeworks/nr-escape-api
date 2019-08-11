<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // TODO: Store new comment
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Select comment by ID
        return Comment::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        // Update comment by ID
        $comment = auth()->user()->comments()->find($id);

        // If no comment, comment doesn't exist or isn't owned by user
        if (!$comment) return $this->unauthorized();

        // if comment updated response success, else response with error
        if ( $comment->fill($request->all())->save() ) {
            return response()->json('success', 200);
        } else {
            return response()->json([
                'error' => 'Comment could not be updated'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        // Delete comment by ID
        $comment = auth()->user()->comments()->find($id);

        // If no comment, comment doesn't exist or isn't owned by user
        if (!$comment) return $this->unauthorized();

        // if comment deleted response success, else response with error
        if ( $comment->delete() ) {
            return response()->json('success', 200);
        } else {
            return response()->json([
                'error' => 'Comment could not be deleted'
            ], 500);
        }
    }

    // Respond with unauthorized
    private function unauthorized() {
        return response()->json([
            'error' => 'Comment doesn\'t exist or not owned by user'
        ], 400);
    }
}
