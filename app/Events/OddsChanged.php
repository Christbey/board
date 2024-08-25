<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class OddsChanged
{
    use Dispatchable, SerializesModels;

    public Model $homeTeam;
    public Model $awayTeam;
    public $messageText;

    public function __construct(Model $homeTeam, Model $awayTeam, string $messageText)
    {
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
        $this->messageText = $messageText;
    }
}
