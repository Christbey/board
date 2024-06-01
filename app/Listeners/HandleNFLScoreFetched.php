<?php

// app/Listeners/HandleNFLScoreFetched.php
namespace App\Listeners;

use App\Events\NFLScoreFetched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleNFLScoreFetched implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NFLScoreFetched $event)
    {
        // Process the scores, for example, log them or update an in-memory store
        // This example simply logs the scores
        \Log::info('NFL Scores Fetched', ['scores' => $event->scores]);
    }
}
