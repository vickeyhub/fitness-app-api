<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/profile', [Api\UserController::class, 'show']);
    Route::put('/user/profile', [Api\UserController::class, 'updateProfile']);
});

Route::post('signup',[Api\AuthController::class, 'register']);
Route::post('verify-otp', [Api\AuthController::class, 'verifyOtp']);
Route::post('login',[Api\AuthController::class, 'login']);
Route::post('forgot-password', [Api\PasswordResetController::class, 'forgotPassword']);
Route::post('reset-password', [Api\PasswordResetController::class, 'resetPassword']);
