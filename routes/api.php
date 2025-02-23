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
    Route::post('bookings', [Api\BookingsController::class, 'createBookings']);
    Route::get('bookings', [Api\BookingsController::class, 'index']);
    Route::post('sessions', [Api\SessionsController::class, 'store']);
    Route::post('bookmark', [Api\SessionsController::class, 'save_bookmark']);
    Route::get('bookmark', [Api\SessionsController::class, 'get_bookmarked_sessions']);
});

Route::post('signup',[Api\AuthController::class, 'register']);
Route::post('verify-otp', [Api\AuthController::class, 'verifyOtp']);
Route::post('login',[Api\AuthController::class, 'login']);
Route::post('forgot-password', [Api\PasswordResetController::class, 'forgotPassword']);
Route::post('reset-password', [Api\PasswordResetController::class, 'resetPassword']);

// trainers/gym api for search and bookings routes
Route::get('trainers', [Api\UserController::class, 'getTrainers']);
Route::get('gyms', [Api\GymsController::class, 'getGyms']);

// sessions/classes management
// Route::apiResource('sessions', Api\SessionsController::class);
Route::post('search-sessions', [Api\SessionsController::class, 'index']);


Route::get('login', function (){
    $output = [
        'status' => 'faild',
        'message' => 'Not Authorised',
    ];
    return response()->json($output, 401);
})->name('login');

