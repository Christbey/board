<?php

namespace App\Providers;


use App\Events\CalculateEloRating;
use App\Events\CalculateExpectedScores;
use App\Events\CalculateStadiumDistance;
use App\Events\NflNewsFetched;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Events\UserMadePick;
use App\Listeners\CalculateEloRatingListener;
use App\Listeners\HandleDistanceCalculation;
use App\Listeners\HandleExpectedScoreCalculation;
use App\Listeners\ProcessNflNews;
use App\Listeners\ProcessUserPick;
use App\Listeners\TaskEventListeners;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        TaskCreated::class => [
            [TaskEventListeners::class, 'handleTaskCreated'],
        ],
        TaskUpdated::class => [
            [TaskEventListeners::class, 'handleTaskUpdated'],
        ],
        TaskDeleted::class => [
            [TaskEventListeners::class, 'handleTaskDeleted'],
        ],

        CalculateExpectedScores::class => [
            HandleExpectedScoreCalculation::class,
        ],
        CalculateStadiumDistance::class => [
            HandleDistanceCalculation::class,
        ],
        CalculateEloRating::class => [
            CalculateEloRatingListener::class,
        ],
        UserMadePick::class => [
            ProcessUserPick::class,
        ],

        // other events...
    ];


    public function boot()
    {
        parent::boot();
    }
}