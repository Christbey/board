<?php

namespace App\Providers;

use App\Events\NbaOddsFetched;
use App\Events\NcaaOddsFetched;
use App\Events\NflOddsFetched;
use App\Events\MlbOddsFetched;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Listeners\StoreNbaOdds;
use App\Listeners\StoreNcaaOdds;
use App\Listeners\StoreNflOdds;
use App\Listeners\StoreMlbOdds;
use App\Listeners\TaskEventListeners;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NflOddsFetched::class => [
            StoreNflOdds::class,
        ],
        MlbOddsFetched::class => [
            StoreMlbOdds::class,
        ],
        NbaOddsFetched::class => [
            StoreNbaOdds::class,
        ],

        NcaaOddsFetched::class => [
            StoreNcaaOdds::class,
        ],
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