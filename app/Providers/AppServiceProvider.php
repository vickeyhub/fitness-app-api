<?php

namespace App\Providers;

use App\Models\Classes;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapThree();

        Route::bind('classes', function (string $value) {
            return Classes::where('id', $value)->firstOrFail();
        });
    }
}
