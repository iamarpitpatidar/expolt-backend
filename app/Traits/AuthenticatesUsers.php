<?php

namespace App\Traits;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

trait AuthenticatesUsers
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if (!Auth::user()->status) {
                return $this->sendBlockedUserResponse();
            }

            Auth::user()->tokens()->delete();
            Auth::logoutOtherDevices($request->get('password'));
            return $this->sendLoginResponse();
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse();
    }

    protected function attemptLogin(LoginRequest $request): bool
    {
        $credentials = [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ];

        return Auth::attempt($credentials);
    }

    protected function sendLoginResponse(): JsonResponse
    {
        $token = auth()->user()?->createToken('authToken', ['*'], now()->addWeek());

        return response()->json(['status' => 'success', 'message' => 'Logged in successfully!', 'data' => ['token' => $token?->plainTextToken]]);
    }

    protected function sendFailedLoginResponse(): JsonResponse
    {
        return response()->json(['status' => 'error', 'status_code' => 'INVALID_CREDENTIALS', 'message' => 'Username or Password incorrect!'], 401);
    }

    public function sendBlockedUserResponse(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'status_code' => 'USER_BLOCKED',
            'message' => 'User is deactivated!'
        ], 401);
    }
}
