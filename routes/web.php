<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RedirectIfAuthenticated;

Route::get('/', function () {
    return view('welcome');
});
Route::get('login', [AuthController::class, 'index'])->name('web-login');
Route::post('login', [AuthController::class, 'login'])->name('post-login');



Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('admin/dashboard', [Admin\DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('chart-data-line',[Admin\DashboardController::class, 'newChartDataLine']);
    Route::get('admin/users', [Admin\UsersController::class, 'index'])->name('admin.users');
    Route::post('admin/users', [Admin\UsersController::class, 'store'])->name('admin.users.store');
    Route::get('admin/users/{user}', [Admin\UsersController::class, 'show'])->name('admin.users.show');
    Route::put('admin/users/{user}', [Admin\UsersController::class, 'update'])->name('admin.users.update');
    Route::delete('admin/users/{user}', [Admin\UsersController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('admin/classes', [Admin\ClassesController::class, 'index'])->name('admin.classes.index');
    Route::post('admin/classes', [Admin\ClassesController::class, 'store'])->name('admin.classes.store');
    Route::get('admin/classes/{classes}', [Admin\ClassesController::class, 'show'])->name('admin.classes.show');
    Route::put('admin/classes/{classes}', [Admin\ClassesController::class, 'update'])->name('admin.classes.update');
    Route::delete('admin/classes/{classes}', [Admin\ClassesController::class, 'destroy'])->name('admin.classes.destroy');

    Route::get('admin/session-catalog', [Admin\SessionCatalogController::class, 'index'])->name('admin.session-catalog.index');
    Route::post('admin/session-catalog', [Admin\SessionCatalogController::class, 'store'])->name('admin.session-catalog.store');
    Route::put('admin/session-catalog/{session_catalog_item}', [Admin\SessionCatalogController::class, 'update'])->name('admin.session-catalog.update');
    Route::delete('admin/session-catalog/{session_catalog_item}', [Admin\SessionCatalogController::class, 'destroy'])->name('admin.session-catalog.destroy');

    Route::get('admin/bookings', [Admin\BookingsController::class, 'index'])->name('admin.bookings.index');
    Route::post('admin/bookings', [Admin\BookingsController::class, 'store'])->name('admin.bookings.store');
    Route::get('admin/bookings/{booking}', [Admin\BookingsController::class, 'show'])->name('admin.bookings.show');
    Route::put('admin/bookings/{booking}', [Admin\BookingsController::class, 'update'])->name('admin.bookings.update');
    Route::delete('admin/bookings/{booking}', [Admin\BookingsController::class, 'destroy'])->name('admin.bookings.destroy');

    Route::get('admin/payments', [Admin\PaymentsController::class, 'index'])->name('admin.payments.index');
    Route::get('admin/payments/{payment}', [Admin\PaymentsController::class, 'show'])->name('admin.payments.show');

    Route::get('admin/posts', [Admin\PostsController::class, 'index'])->name('admin.posts.index');
    Route::post('admin/posts', [Admin\PostsController::class, 'store'])->name('admin.posts.store');
    Route::get('admin/posts/{post}', [Admin\PostsController::class, 'show'])->name('admin.posts.show');
    Route::put('admin/posts/{post}', [Admin\PostsController::class, 'update'])->name('admin.posts.update');
    Route::post('admin/posts/{post}/like', [Admin\PostsController::class, 'like'])->name('admin.posts.like');
    Route::post('admin/posts/{post}/comments', [Admin\PostsController::class, 'comment'])->name('admin.posts.comment');
    Route::delete('admin/posts/{post}', [Admin\PostsController::class, 'destroy'])->name('admin.posts.destroy');

    Route::get('admin/comments', [Admin\CommentsController::class, 'index'])->name('admin.comments.index');
    Route::delete('admin/comments/{comment}', [Admin\CommentsController::class, 'destroy'])->name('admin.comments.destroy');

    Route::get('admin/statuses', [Admin\StatusesController::class, 'index'])->name('admin.statuses.index');
    Route::delete('admin/statuses/{status}', [Admin\StatusesController::class, 'destroy'])->name('admin.statuses.destroy');

    Route::get('admin/tags', [Admin\TagsController::class, 'index'])->name('admin.tags.index');
    Route::post('admin/tags', [Admin\TagsController::class, 'store'])->name('admin.tags.store');
    Route::put('admin/tags/{tag}', [Admin\TagsController::class, 'update'])->name('admin.tags.update');
    Route::delete('admin/tags/{tag}', [Admin\TagsController::class, 'destroy'])->name('admin.tags.destroy');
});
