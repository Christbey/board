<?php

namespace App\Providers;


use App\Models\CollegeFootballGame;
use App\Models\NcaaOdds;
use App\Models\NflEspnEvent;
use App\Observers\Cfb\CollegeFootballGameObserver;
use App\Observers\Cfb\NcaaOddsObserver;
use App\Observers\NflEspnEventObserver;
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

        NflEspnEvent::observe(NflEspnEventObserver::class);
        CollegeFootballGame::observe(CollegeFootballGameObserver::class);
        NcaaOdds::observe(NcaaOddsObserver::class);


        // Other bootstrapping logic
    }
}
