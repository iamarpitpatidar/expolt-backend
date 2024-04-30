<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Send a password reset link to the given user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'), function($user, $token) {
            $user->sendPasswordResetNotification($token);
            return Password::RESET_LINK_SENT;
        });

        return ($status === Password::RESET_LINK_SENT || $status === Password::INVALID_USER)
            ? $this->sendResponse(__('lang.passwords.sent'))
            : $this->sendErrorResponse('Unable to send reset link, please try again in some time.', 400);
    }

    /**
     * Reset the given user's password.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email:dns',
            'token' => 'required|string',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()?->uncompromised(5)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? $this->sendResponse('Password reset successfully')
            : $this->sendErrorResponse($status == Password::INVALID_TOKEN ? 'Invalid token' : 'Unable to reset password', 400);
    }
}
