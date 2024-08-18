<?php

namespace App\Providers;


use App\Models\NflEspnEvent;
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


        // Other bootstrapping logic
    }
}
