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
    Route::get('admin/users/trainers', [Admin\UsersController::class, 'trainers'])->name('admin.users.trainers');
    Route::get('admin/users/gyms', [Admin\UsersController::class, 'gyms'])->name('admin.users.gyms');
    Route::post('admin/users', [Admin\UsersController::class, 'store'])->name('admin.users.store');
    Route::get('admin/users/{user}', [Admin\UsersController::class, 'show'])->name('admin.users.show');
    Route::put('admin/users/{user}', [Admin\UsersController::class, 'update'])->name('admin.users.update');
    Route::delete('admin/users/{user}', [Admin\UsersController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('admin/classes', [Admin\ClassesController::class, 'index'])->name('admin.classes.index');
    Route::post('admin/classes', [Admin\ClassesController::class, 'store'])->name('admin.classes.store');
    Route::get('admin/classes/{classes}', [Admin\ClassesController::class, 'show'])->name('admin.classes.show');
    Route::put('admin/classes/{classes}', [Admin\ClassesController::class, 'update'])->name('admin.classes.update');
    Route::delete('admin/classes/{classes}', [Admin\ClassesController::class, 'destroy'])->name('admin.classes.destroy');

    Route::get('admin/exercise-categories', [Admin\ExerciseCategoriesController::class, 'index'])->name('admin.exercise-categories.index');
    Route::post('admin/exercise-categories', [Admin\ExerciseCategoriesController::class, 'store'])->name('admin.exercise-categories.store');
    Route::get('admin/exercise-categories/{exercise_category}', [Admin\ExerciseCategoriesController::class, 'show'])->name('admin.exercise-categories.show');
    Route::put('admin/exercise-categories/{exercise_category}', [Admin\ExerciseCategoriesController::class, 'update'])->name('admin.exercise-categories.update');
    Route::delete('admin/exercise-categories/{exercise_category}', [Admin\ExerciseCategoriesController::class, 'destroy'])->name('admin.exercise-categories.destroy');

    Route::get('admin/exercises', [Admin\ExercisesController::class, 'index'])->name('admin.exercises.index');
    Route::post('admin/exercises', [Admin\ExercisesController::class, 'store'])->name('admin.exercises.store');
    Route::get('admin/exercises/{exercise}', [Admin\ExercisesController::class, 'show'])->name('admin.exercises.show');
    Route::put('admin/exercises/{exercise}', [Admin\ExercisesController::class, 'update'])->name('admin.exercises.update');
    Route::delete('admin/exercises/{exercise}', [Admin\ExercisesController::class, 'destroy'])->name('admin.exercises.destroy');

    Route::get('admin/workout-plans', [Admin\WorkoutPlansController::class, 'index'])->name('admin.workout-plans.index');
    Route::post('admin/workout-plans', [Admin\WorkoutPlansController::class, 'store'])->name('admin.workout-plans.store');
    Route::get('admin/workout-plans/{workout_plan}', [Admin\WorkoutPlansController::class, 'show'])->name('admin.workout-plans.show');
    Route::put('admin/workout-plans/{workout_plan}', [Admin\WorkoutPlansController::class, 'update'])->name('admin.workout-plans.update');
    Route::delete('admin/workout-plans/{workout_plan}', [Admin\WorkoutPlansController::class, 'destroy'])->name('admin.workout-plans.destroy');

    Route::get('admin/workout-logs', [Admin\WorkoutLogsController::class, 'index'])->name('admin.workout-logs.index');
    Route::post('admin/workout-logs', [Admin\WorkoutLogsController::class, 'store'])->name('admin.workout-logs.store');
    Route::get('admin/workout-logs/{workout_log}', [Admin\WorkoutLogsController::class, 'show'])->name('admin.workout-logs.show');
    Route::put('admin/workout-logs/{workout_log}', [Admin\WorkoutLogsController::class, 'update'])->name('admin.workout-logs.update');
    Route::delete('admin/workout-logs/{workout_log}', [Admin\WorkoutLogsController::class, 'destroy'])->name('admin.workout-logs.destroy');
    Route::get('admin/exercise-logs', [Admin\ExerciseLogsController::class, 'index'])->name('admin.exercise-logs.index');

    Route::get('admin/nutrition/meals', [Admin\NutritionMealsController::class, 'index'])->name('admin.nutrition.meals.index');
    Route::post('admin/nutrition/meals', [Admin\NutritionMealsController::class, 'store'])->name('admin.nutrition.meals.store');
    Route::get('admin/nutrition/meals/{nutrition_meal}', [Admin\NutritionMealsController::class, 'show'])->name('admin.nutrition.meals.show');
    Route::put('admin/nutrition/meals/{nutrition_meal}', [Admin\NutritionMealsController::class, 'update'])->name('admin.nutrition.meals.update');
    Route::delete('admin/nutrition/meals/{nutrition_meal}', [Admin\NutritionMealsController::class, 'destroy'])->name('admin.nutrition.meals.destroy');

    Route::get('admin/nutrition/targets', [Admin\NutritionTargetsController::class, 'index'])->name('admin.nutrition.targets.index');
    Route::post('admin/nutrition/targets', [Admin\NutritionTargetsController::class, 'store'])->name('admin.nutrition.targets.store');
    Route::get('admin/nutrition/targets/{nutrition_target}', [Admin\NutritionTargetsController::class, 'show'])->name('admin.nutrition.targets.show');
    Route::put('admin/nutrition/targets/{nutrition_target}', [Admin\NutritionTargetsController::class, 'update'])->name('admin.nutrition.targets.update');
    Route::delete('admin/nutrition/targets/{nutrition_target}', [Admin\NutritionTargetsController::class, 'destroy'])->name('admin.nutrition.targets.destroy');
    Route::get('admin/nutrition/adherence', [Admin\NutritionAdherenceController::class, 'index'])->name('admin.nutrition.adherence.index');

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
    Route::post('admin/posts/{post}/toggle-visibility', [Admin\PostsController::class, 'toggleVisibility'])->name('admin.posts.toggle-visibility');
    Route::post('admin/posts/{postId}/restore', [Admin\PostsController::class, 'restore'])->name('admin.posts.restore');
    Route::delete('admin/posts/{post}', [Admin\PostsController::class, 'destroy'])->name('admin.posts.destroy');

    Route::get('admin/comments', [Admin\CommentsController::class, 'index'])->name('admin.comments.index');
    Route::post('admin/comments/{comment}/toggle-visibility', [Admin\CommentsController::class, 'toggleVisibility'])->name('admin.comments.toggle-visibility');
    Route::delete('admin/comments/{comment}', [Admin\CommentsController::class, 'destroy'])->name('admin.comments.destroy');

    Route::get('admin/statuses', [Admin\StatusesController::class, 'index'])->name('admin.statuses.index');
    Route::post('admin/statuses/{status}/toggle-visibility', [Admin\StatusesController::class, 'toggleVisibility'])->name('admin.statuses.toggle-visibility');
    Route::delete('admin/statuses/{status}', [Admin\StatusesController::class, 'destroy'])->name('admin.statuses.destroy');
    Route::get('admin/follows', [Admin\FollowsController::class, 'index'])->name('admin.follows.index');
    Route::delete('admin/follows/{follow}', [Admin\FollowsController::class, 'destroy'])->name('admin.follows.destroy');

    Route::get('admin/tags', [Admin\TagsController::class, 'index'])->name('admin.tags.index');
    Route::post('admin/tags', [Admin\TagsController::class, 'store'])->name('admin.tags.store');
    Route::put('admin/tags/{tag}', [Admin\TagsController::class, 'update'])->name('admin.tags.update');
    Route::delete('admin/tags/{tag}', [Admin\TagsController::class, 'destroy'])->name('admin.tags.destroy');
});
