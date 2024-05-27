<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Events\TaskDeleted;

class TaskEventListeners
{
    public function handleTaskCreated(TaskCreated $event)
    {
        // Handle task created logic here
        // e.g., Mail::to($event->task->user)->send(new TaskCreatedMail($event->task));
    }

    public function handleTaskUpdated(TaskUpdated $event)
    {
        // Handle task updated logic here
        // e.g., Log::info('Task updated: ' . $event->task->id);
    }

    public function handleTaskDeleted(TaskDeleted $event)
    {
        // Handle task deleted logic here
        // e.g., Log::info('Task deleted: ' . $event->task->id);
    }
}
