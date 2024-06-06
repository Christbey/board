<?php

namespace App\Providers;

use App\Events\NbaOddsFetched;
use App\Events\NbaScoresFetched;
use App\Events\NcaaOddsFetched;
use App\Events\NflOddsFetched;
use App\Events\MlbOddsFetched;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Listeners\ProcessNbaScores;
use App\Listeners\StoreNbaOdds;
use App\Listeners\StoreNcaaOdds;
use App\Listeners\StoreNflOdds;
use App\Listeners\StoreMlbOdds;
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


        // other events...
    ];

    public function boot()
    {
        parent::boot();
    }
}