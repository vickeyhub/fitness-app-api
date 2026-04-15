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
});
