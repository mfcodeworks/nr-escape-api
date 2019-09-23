<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle user password reset requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgot(Request $request) {
        // Send password reset email to user
        $response = $this->broker()->sendResetLink($request->all());
        if ($response === Password::RESET_LINK_SENT) {
            return response()->json('success', 204);
        } else {
            return response()->json([
                'error' => 'Unable to send reset link',
                'payload' => $response
            ], 401);
        }
    }
}
