<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NbaOddsFetched
{
    use Dispatchable, SerializesModels;

    public $odds;

    /**
     * Create a new event instance.
     *
     * @param array $odds
     */
    public function __construct(array $odds)
    {
        $this->odds = $odds;
    }
}