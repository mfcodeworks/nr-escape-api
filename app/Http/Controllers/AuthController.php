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
use Storage;

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
            'username' => 'required|min:3|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        if($validator->fails()) {
            return response()->json([
                'error' => 'Unable to create new account, check your details',
                'validator' => $validator->errors()
            ], 400);
        }

        // Create new user with details DEBUG: Add guest auth
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Email out welcome email
        $beautymail = app()->make('Snowfire\Beautymail\Beautymail');
        $beautymail->send('emails.welcome', [], function($message) use ($user) {
            $message->from('mua@nygmarosebeauty.com', 'NR Escape')
                ->to($user->email, $user->username)
                ->subject('Welcome to Escape');
        });

        // Signup success response
        return response()->json([
            'token' => $user->createToken(env('APP_NAME', 'Escape'))->accessToken,
            'email' => $user->email,
            'settings' => $user->settings,
            'profile' => $user->makeVisible(['fcm_token', 'email'])
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

            // Dispatch login event
            if ($user->settings['unknownDevices']) {
                $agent = new Agent();
                event(new UserSignin([
                    'ip' => $request->ip(),
                    'device' => $agent->device(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                    'robot' => $agent->isRobot(),
                    'user_id' => auth()->user()->id
                ]));
            }

            // Return successful response
            return response()->json([
                'token' => auth()->user()->createToken(env('APP_NAME', 'Escape'))->accessToken,
                'email' => auth()->user()->email,
                'settings' => auth()->user()->settings,
                'profile' => auth()->user()->makeVisible(['fcm_token', 'email'])
            ], 201);
        }

        // If auth fails respond with error
        return response()->json(['error' => 'Incorrect username or password'], 401);
    }

    /**
     * Return the authenticated users details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request) {
        // Return user with hidden data
        if (auth()->user()->can('view', auth()->user())) {
            return response()->json(
                auth()->user()->makeVisible(['fcm_token', 'email'])
            );
        }
    }

    /**
     * Update user data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request) {
        if (auth()->user()->can('update', auth()->user())) {
            // Instantiate user data
            $data = $request->all();

            // If updating password, hash new password
            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            // If profile pic is present as file, handle the file (either URL or image/video)
            if ($request->hasFile('media')) {
                $user = auth()->user()->id;
                $path = Storage::disk('spaces')->put("SocialHub/author/{$user}/profile", $request->media, 'public');
                $data['profile_pic'] = Storage::disk('spaces')->url($path);
                unset($data['media']);
            }
            unset($data['_method']);

            // Save and return authorised user account
            auth()->user()->fill($data)->save();
            return $this->user($request);
        }
    }

    /**
     * Deactivate user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(Request $request) {
        // Set deactivated status
        if ($user->can('deactivate', auth()->user())) {
            auth()->user()->fill([
                'deactivated' => 1
            ])->save();
            return response()->json('success', 204);
        }

        return response()->json([
            'error' => 'Couldn\'t deactivate user account'
        ], 500);
    }
}