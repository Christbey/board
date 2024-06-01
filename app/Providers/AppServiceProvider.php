<?php

namespace App\Providers;

use App\Services\NflOddsService;
use App\Services\NcaaOddsService;
use App\Services\MlbOddsService;
use App\Services\NbaOddsService;
use App\Services\NFLScoresService;
use App\Services\OddsService;
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
            return new NflOddsService(new OddsService);
        });

        $this->app->singleton(NcaaOddsService::class, function ($app) {
            return new NcaaOddsService(new OddsService);
        });

        $this->app->singleton(MlbOddsService::class, function ($app) {
            return new MlbOddsService(new OddsService);
        });

        $this->app->singleton(NbaOddsService::class, function ($app) {
            return new NbaOddsService(new OddsService);
        });

        $this->app->singleton(NFLScoresService::class, function ($app) {
            return new NFLScoresService();
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
