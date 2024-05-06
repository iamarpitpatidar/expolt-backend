<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::all();
        $users = fractal($users, new UserTransformer())->parseIncludes('status');
        return $this->sendResponse($users->toArray());
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = User::query()->create(array_merge($data, ['email_verified_at' => now()]));
        $user->assignRole($data['role']);

        return $this->sendResponse('User created successfully');
    }

    public function show(User $user): JsonResponse
    {
        $user = fractal($user, new UserTransformer())->parseIncludes('status');
        return $this->sendResponse($user->toArray());
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });

        $user->update($data);
        return $this->sendResponse('User updated successfully');
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return $this->sendResponse('User deleted successfully');
    }

    public function showProfile(): JsonResponse
    {
        $user = fractal(auth()->user(), new UserTransformer());
        return $this->sendResponse($user->toArray());
    }
}
