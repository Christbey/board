<?php

namespace App\Providers;

use App\Models\Odds;
use App\Observers\OddsObserver;
use Illuminate\Support\ServiceProvider;
use App\Services\NflOddsService;
use App\Services\NcaaOddsService;
use App\Services\MlbOddsService;
use App\Services\NbaOddsService;
use App\Services\OddsApiService;

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
            return new NflOddsService(new OddsApiService);
        });

        $this->app->singleton(NcaaOddsService::class, function ($app) {
            return new NcaaOddsService(new OddsApiService);
        });

        $this->app->singleton(MlbOddsService::class, function ($app) {
            return new MlbOddsService(new OddsApiService);
        });

        $this->app->singleton(NbaOddsService::class, function ($app) {
            return new NbaOddsService(new OddsApiService);
        });
    }



/**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Odds::observe(OddsObserver::class);

        //
    }
}
