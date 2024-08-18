<?php

namespace App\Providers;


use App\Events\NflNewsFetched;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Listeners\ProcessNflNews;
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

        NflNewsFetched::class => [
            ProcessNflNews::class,
        ],

        // other events...
    ];


    public function boot()
    {
        parent::boot();
    }
}