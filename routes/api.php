<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VirtualMachineController;
use App\Http\Middleware\isAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return response()->json(['success' => true, 'timestamp' => now()]);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('users/me', [UserController::class, 'showProfile']);
    Route::get('apps/list', [AppController::class, 'listApps']);
    Route::get('apps/{uuid}/virtual-machine', [VirtualMachineController::class, 'show']);

    Route::group(['middleware' => isAdmin::class], function () {
        Route::apiResource('apps', AppController::class);
        Route::apiResource('users', UserController::class);

        Route::get('settings', [SettingsController::class, 'index']);
        Route::patch('settings', [SettingsController::class, 'update']);
    });
});

Route::get('whoami', [VirtualMachineController::class, 'whoAmI']);
