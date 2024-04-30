<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\AuthenticatesUsers;
use App\Traits\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuthController extends Controller
{
    use AuthenticatesUsers, ThrottlesLogins;

    public function logout(Request $request): void
    {
        Auth::logout();
        Auth::user()?->tokens()->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
