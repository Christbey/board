<?php

namespace App\Providers;

use App\Services\NflOddsService;
use App\Services\NcaaOddsService;
use App\Services\MlbOddsService;
use App\Services\NbaOddsService;
use App\Services\NflScoresService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Other bootstrapping logic
    }
}
