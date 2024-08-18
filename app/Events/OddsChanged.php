<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class OddsChanged
{
    use SerializesModels;

    public $homeTeam;
    public $awayTeam;
    public $existingOdds;
    public $newOdds;

    public function __construct($homeTeam, $awayTeam, $existingOdds, $newOdds)
    {
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
        $this->existingOdds = $existingOdds;
        $this->newOdds = $newOdds;
    }
}
