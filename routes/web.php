<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [AuthController::class, 'index'])->name('web-login');
Route::post('login', [AuthController::class, 'login'])->name('post-login');
Route::get('admin/dashboard', [Admin\DashboardController::class, 'dashboard'])->name('admin.dashboard');
Route::get('admin/users', [Admin\UsersController::class, 'index'])->name('admin.users');
