<?php

namespace App\Listeners;

use App\Events\NbaScoresFetched;
use Illuminate\Support\Facades\Log;

class ProcessNbaScores
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\NbaScoresFetched  $event
     * @return void
     */
    public function handle(NbaScoresFetched $event)
    {
        $scores = $event->scores;

        // Process the scores as needed
        Log::info('Processing NBA Scores: ', $scores);

        // Optionally store the scores in the database or perform other actions
    }
}
