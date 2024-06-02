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
        $this->app->singleton(NflOddsService::class, function ($app) {
            return new NflOddsService();
        });

        $this->app->singleton(NcaaOddsService::class, function ($app) {
            return new NcaaOddsService();
        });

        $this->app->singleton(MlbOddsService::class, function ($app) {
            return new MlbOddsService();
        });

        $this->app->singleton(NbaOddsService::class, function ($app) {
            return new NbaOddsService();
        });

        $this->app->singleton(NflScoresService::class, function ($app) {
            return new NflScoresService();
        });

        // Register Horizon and Telescope service providers
        $this->app->register(HorizonServiceProvider::class);
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
