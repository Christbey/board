<?php

namespace App\Events;

class CalculateStadiumDistance
{
    public $homeTeamId;
    public $awayTeamId;

    public function __construct($homeTeamId, $awayTeamId)
    {
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
    }
}
