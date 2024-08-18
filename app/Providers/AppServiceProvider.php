<?php

namespace App\Providers;


use App\Models\CollegeFootballGame;
use App\Models\NflEspnEvent;
use App\Observers\CollegeFootballGameObserver;
use App\Observers\NflEspnEventObserver;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use NotificationChannels\Discord\Discord;
use NotificationChannels\Discord\DiscordChannel;

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

        $this->app->make(ChannelManager::class)->extend('discord', function ($app) {
            return new DiscordChannel($app->make(Discord::class));
        });
        // Other bootstrapping logic
    }
}
