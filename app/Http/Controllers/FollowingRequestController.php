<?php

namespace App\Http\Controllers;

use App\FollowingRequest;
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
        $followingRequest = FollowingRequest::where('user', '=', $id)
            ->where('following_user', '=', auth()->user()->id)
            ->first();

        return response()->json(
            $followingRequest->approve()
        );
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
        $followingRequest = FollowingRequest::where('user', '=', $id)
            ->where('following_user', '=', auth()->user()->id)
            ->first();

        return response()->json(
            $followingRequest->decline()
        );
    }

    /**
     * Return user following requests
     *
     * @return \Illuminate\Http\Response
     */
    public function requests() {
        return response()->json(
            auth()->user()->followingRequest
        );
    }
}
