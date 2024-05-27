<?php

namespace App\Providers;

use App\Events\OddsFetched;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskDeleted;
use App\Listeners\StoreOdds;
use App\Listeners\TaskEventListeners;
use App\Models\Odds;
use App\Observers\OddsObserver;
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
        OddsFetched::class => [
            StoreOdds::class,
        ],
    ];

    public function boot()
    {
        Odds::observe(OddsObserver::class);

        parent::boot();
    }
}
