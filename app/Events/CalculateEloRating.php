<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CalculateEloRating
{
    use Dispatchable, SerializesModels;

    public $teamId;
    public $seasonType;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($teamId, $seasonType = 'Regular Season')
    {
        $this->teamId = $teamId;
        $this->seasonType = $seasonType;
    }
}
