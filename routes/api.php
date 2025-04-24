<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/profile', [Api\UserController::class, 'show']);
    Route::post('/user/profile', [Api\UserController::class, 'updateProfile']);
    Route::post('bookings', [Api\BookingsController::class, 'createBookings']);
    Route::get('bookings', [Api\BookingsController::class, 'index']);
    Route::get('sessions', [Api\SessionsController::class, 'index']);
    Route::delete('sessions/{id}', [Api\SessionsController::class, 'destroy']);
    Route::post('sessions', [Api\SessionsController::class, 'store']);
    Route::post('bookmark', [Api\SessionsController::class, 'save_bookmark']);
    Route::get('bookmark', [Api\SessionsController::class, 'get_bookmarked_sessions']);
    Route::get('session/session-filter-api', [Api\SessionFilterController::class, 'session_filter_data']);
    Route::post('/create-payment-intent', [Api\PaymentController::class, 'createPaymentIntent']);
    Route::post('/confirm-payment', [Api\PaymentController::class, 'confirmPayment']);
    Route::get('fetch-active-plans', [Api\SessionsController::class, 'fetchActivePlans']);

    Route::get('owner-bookings', [Api\GymsController::class, 'getBookingsFromUsers']);

    // newsfeed controllers
    // Posts
    Route::apiResource('posts', Api\PostController::class);
    // Route::get('posts', [Api\PostController::class,'index']);
    // Route::post('posts', [Api\PostController::class,'store']);

    // Likes
    Route::post('/posts/{post}/like', [Api\LikeController::class, 'like']);
    Route::post('/posts/{post}/dislike', [Api\LikeController::class, 'dislike']);

    // Comments
    Route::get('/posts/{post}/comment', [Api\CommentController::class, 'index']);

    Route::post('/posts/{post}/comment', [Api\CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [Api\CommentController::class, 'destroy']);

    // Tags
    Route::get('/tags', [Api\TagController::class, 'index']);
    Route::post('/tags', [Api\TagController::class, 'store']);
});

Route::post('signup', [Api\AuthController::class, 'register']);
Route::post('verify-otp', [Api\AuthController::class, 'verifyOtp']);
Route::post('login', [Api\AuthController::class, 'login']);
Route::post('forgot-password', [Api\PasswordResetController::class, 'forgotPassword']);
Route::post('reset-password', [Api\PasswordResetController::class, 'resetPassword']);

// trainers/gym api for search and bookings routes
Route::get('trainers', [Api\UserController::class, 'getTrainers']);
Route::get('gyms', [Api\GymsController::class, 'getGyms']);

// sessions/classes management
Route::post('search-sessions', [Api\SessionsController::class, 'search_sessions']);
Route::get('session-detail/{id}', [Api\SessionsController::class, 'session_detail']);
Route::post('/stripe/webhook', [Api\StripeWebhookController::class, 'handle']);


Route::get('login', function () {
    $output = [
        'status' => 'faild',
        'message' => 'Not Authorised',
    ];
    return response()->json($output, 401);
})->name('login');

