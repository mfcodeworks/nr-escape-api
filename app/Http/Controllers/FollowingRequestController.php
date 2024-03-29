<?php

namespace App\Http\Controllers;

use App\FollowingRequest;
use App\User;
use Illuminate\Http\Request;

class FollowingRequestController extends Controller
{
    /**
     * Approve following request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, $id)
    {
        if (auth()->user()->can('update', auth()->user())) {
            $followingRequest = FollowingRequest::where('user', $id)
                ->where('following_user', auth()->user()->id)
                ->first();

            return response()->json(
                $followingRequest->approve()
            );
        }
        return response()->json([
            'error' => 'Cannot approve request'
        ], 401);
    }

    /**
     * Decline following request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function decline(Request $request, $id)
    {
        if (auth()->user()->can('update', auth()->user())) {
            $followingRequest = FollowingRequest::where('user', $id)
                ->where('following_user', auth()->user()->id)
                ->first();

            return response()->json(
                $followingRequest->decline()
            );
        }
        return response()->json([
            'error' => 'Cannot decline request'
        ], 401);
    }

    /**
     * Return user following requests
     *
     * @return \Illuminate\Http\Response
     */
    public function requests() {
        if (auth()->user()->can('update', auth()->user())) {
            return response()->json(
                auth()->user()->followingRequest
            );
        }
    }

    /**
     * Return if user has requested to follow profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function requested(Request $request, $id) {
        return response()->json(
            !!User::findOrFail($id)
                ->followingRequest()
                ->where('user', auth()->user()->id)
                ->first()
        );
    }
}
