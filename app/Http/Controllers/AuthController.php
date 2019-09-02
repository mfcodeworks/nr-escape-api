<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use App\User;
use App\Events\UserSignin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller
{
    /**
     * Handle user registration requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        // Validate new user info
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        if($validator->fails()) {
            return response()->json([
                'error' => 'Unable to create new account, check your details',
                'validator' => $validator->errors()
            ], 400);
        }

        // Create new user with details
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Email out welcome email
        $beautymail = app()->make('Snowfire\Beautymail\Beautymail');
        $beautymail->send('emails.welcome', [], function($message) use ($user) {
            $message
                ->from('it@nygmarosebeauty.com')
                ->to($user->email, $user->username)
                ->subject('Welcome to Escape');
        });

        // Get passport token
        $token = $user->createToken(env('APP_NAME', 'Escape'))->accessToken;

        // Signup success response
        return response()->json([
            'token' => $token,
            'email' => $request->email,
            'settings' => [],
            'profile' => $user
        ], 201);
    }

    /**
     * Handle user login requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        // Create credentials object
        $credentials = [
            'username' => $request->username,
            'password' => $request->password
        ];

        // Attempt auth
        if (auth()->attempt($credentials)) {
            // Remove deactivated response from user
            $user = auth()->user();
            $user->deactivated = 0;
            $user->save();

            // Create JWT for access
            $token = auth()->user()->createToken(env('APP_NAME', 'Escape'))->accessToken;

            // Dispatch login event
            $agent = new Agent();
            event(new UserSignin([
                'ip' => $request->ip(),
                'device' => $agent->device(),
                'platform' => $agent->platform(),
                'browser' => $agent->browser(),
                'robot' => $agent->isRobot(),
                'user_id' => auth()->user()->id
            ]));

            // Return successful response
            return response()->json([
                'token' => $token,
                'email' => auth()->user()->email,
                'settings' => [],
                'profile' => auth()->user()
            ], 201);

        // If auth fails respond with error
        }

        return response()->json(['error' => 'Incorrect username or password'], 401);
    }

    /**
     * Return the authenticated users details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request) {
        // Get authenticated user
        $user = auth()->user()->toArray();

        // Fill in hidden data for authenticated user to view
        $user['settings'] = auth()->user()->settings;
        $user['email'] = auth()->user()->email;

        // Return user with hidden data
        return response()->json($user);
    }

    /**
     * Update user data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request) {
        // Instantiate user data
        $data = $request->all();

        // If updating password, hash new password
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // TODO: If profile pic then handle

        // Get authorised user account
        $user = auth()->user()->fill($data)->save();
        return $this->user($request);
    }

    /**
     * Insert FCM Token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fcm(Request $request) {
        // Get user
        $user = auth()->user();

        // Get new token
        $token = $request->token;

        // Set group name
        $groupName = "user.{$user->id}";

        // Get group key if existing
        $groupToken = $user->fcm_token;

        // If group token add, otherwise create
        $key = ($groupToken)
        ? FCMGroup::addToGroup($groupName, $groupToken, $token)
        : FCMGroup::createGroup($groupName, $token);

        // Update group token
        $save = $user->fill([
            'fcm_token' => $key
        ])->save();

        if ($save) return response()->json('success', 204);
    }

    /**
     * Deactivate user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(Request $request) {
        // Get authenticated user
        $user = auth()->user();

        // Set deactivated status
        $user->deactivated = 1;

        // Save updated object
        if ($user->save()) {
            return response()->json('success', 204);
        } else {
            return response()->json([
                'error' => 'Couldn\'t update user account'
            ], 500);
        }
    }
}