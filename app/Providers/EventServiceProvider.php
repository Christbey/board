<?php

namespace App\Providers;


use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
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