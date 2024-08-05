<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\DestroyVirtualMachinesJob;
use App\Traits\AuthenticatesUsers;
use App\Traits\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

final class AuthController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    public function logout(): JsonResponse
    {
        Auth::user()?->tokens()->delete();
        dispatch(new DestroyVirtualMachinesJob(Auth::id()));
        return $this->sendResponse('User logged out successfully');
    }
}
