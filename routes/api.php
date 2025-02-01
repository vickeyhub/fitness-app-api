<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('signup',[Api\UserController::class, 'register']);
Route::post('login',[Api\UserController::class, 'login']);
Route::post('forgot-password', [Api\PasswordResetController::class, 'forgotPassword']);
Route::post('reset-password', [Api\PasswordResetController::class, 'resetPassword']);
