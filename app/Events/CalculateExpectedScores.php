<?php

namespace App\Events;

class CalculateExpectedScores
{
    public $homeTeamElo;
    public $awayTeamElo;
    public $homeTeamId;
    public $awayTeamId;

    public function __construct($homeTeamElo, $awayTeamElo, $homeTeamId, $awayTeamId)
    {
        $this->homeTeamElo = $homeTeamElo;
        $this->awayTeamElo = $awayTeamElo;
        $this->homeTeamId = $homeTeamId;
        $this->awayTeamId = $awayTeamId;
    }
}
