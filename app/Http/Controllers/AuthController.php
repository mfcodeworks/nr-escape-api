<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller
{
    /**
     * Handle user registration requests
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        // Validate new user info
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        if($validator->fails()) {
            return response()->json([
                'error' => 'Unable to create new account, check your details',
                'validator' => $validator->errors()
            ], 401);
        }

        // Create new user with details
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('SocialHub')->accessToken;

        // Signup success response
        return response()->json([
            'token' => $token,
            'email' => $request->email,
            'settings' => [],
            'profile' => $user
        ], 200);
    }

    /**
     * Handle user login requests
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        // Create credentials object
        $credentials = [
            'name' => $request->name,
            'password' => $request->password
        ];

        // Attempt auth
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('SocialHub')->accessToken;
            return response()->json([
                'token' => $token,
                'email' => auth()->user()->email,
                'settings' => [],
                'profile' => auth()->user()
            ], 200);
        // If auth fails respond with error
        } else {
            return response()->json(['error' => 'Incorrect username or password'], 401);
        }
    }

    /**
     * Return the authenticated users details
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request) {
        return response()->json(auth()->user(), 200);
    }
}