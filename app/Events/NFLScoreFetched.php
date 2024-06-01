<?php

// app/Events/NFLScoreFetched.php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NFLScoreFetched
{
    use Dispatchable, SerializesModels;

    public $scores;

    public function __construct($scores)
    {
        $this->scores = $scores;
    }
}
