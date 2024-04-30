<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return response()->json(['success' => true, 'timestamp' => now()]);
});

Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');

    Route::post('forgot-password', 'AuthController@resetPasswordMail');
    Route::post('reset-password', 'AuthController@resetPassword');
});
