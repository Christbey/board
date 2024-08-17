<?php

namespace App\Providers;


use App\Models\CollegeFootballGame;
use App\Observers\CollegeFootballGameObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Other bootstrapping logic
    }
}
