<?php

namespace App\Providers;

use App\Services\NflOddsService;
use App\Services\NcaaOddsService;
use App\Services\MlbOddsService;
use App\Services\NbaOddsService;
use App\Services\NflScoresService;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;
use Laravel\Horizon\HorizonServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {


        // Register Horizon and Telescope service providers
        $this->app->register(TelescopeServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Other bootstrapping logic
    }
}
