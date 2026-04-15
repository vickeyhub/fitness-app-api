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
});
