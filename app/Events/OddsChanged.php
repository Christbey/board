<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\NflTeam;

class OddsChanged
{
    use Dispatchable, SerializesModels;

    public NflTeam $homeTeam;
    public NflTeam $awayTeam;
    public $messageText;

    public function __construct(NflTeam $homeTeam, NflTeam $awayTeam, string $messageText)
    {
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
        $this->messageText = $messageText;
    }
}
