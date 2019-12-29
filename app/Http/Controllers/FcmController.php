<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use App\User;
use FCM;
use FCMGroup;

class FcmController extends Controller
{
    /**
     * Insert FCM Token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function token(Request $request) {
        // Get user
        $user = auth()->user();

        // Get new token
        $token = $request->token;

        // Set group name
        $groupName = "user.{$user->id}";

        // Get group key if existing
        $groupToken = $user->fcm_token;

        Log::alert("Creating group {$groupName} for user {$user->username} with token {$token}");

        // If group token add, otherwise create
        $key = ($groupToken)
            ? FCMGroup::addToGroup($groupName, $groupToken, [$token])
            : FCMGroup::createGroup($groupName, [$token]);

        Log::alert("User {$user->username} saved {$token} to FCM group {$key}");

        // Update group token
        $user->fill([
            'fcm_token' => $key
        ])->save();

        return response()->json('success', 204);
    }

    /**
     * Subscribe to FCM Topic
     *
     * @param Request $request
     * @param string $topic
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request, $topic) {
        $messaging = (new Firebase\Factory())->createMessaging();
        return $messaging->subscribeToTopic($topic, $request->token);
    }

    /**
     * Unsubscribe from FCM Topic
     *
     * @param Request $request
     * @param string $topic
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(Request $request, $topic) {
        $messaging = (new Firebase\Factory())->createMessaging();
        return $messaging->unsubscribeFromTopic($topic, $registrationTokens);
    }
}
