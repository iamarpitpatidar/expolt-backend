<?php

namespace App\Http\Controllers;

use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function show(): JsonResponse
    {
        $user = fractal(auth()->user(), new UserTransformer());
        return $this->sendResponse($user->toArray());
    }
}
