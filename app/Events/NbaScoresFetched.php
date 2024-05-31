<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NbaScoresFetched
{
    use Dispatchable, SerializesModels;

    public $scores;

    /**
     * Create a new event instance.
     *
     * @param array $scores
     */
    public function __construct(array $scores)
    {
        $this->scores = $scores;
        Log::info('NbaScoresFetched event created with scores', $scores);
    }
}
