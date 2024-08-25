<?php

namespace App\Events;

use App\Models\NflEspnEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserMadePick
{
    use Dispatchable, SerializesModels;

    public $user;
    public $event;
    public $selectedTeamId;
    public $isCorrect;

    public function __construct($user, NflEspnEvent $event, $selectedTeamId, $isCorrect)
    {
        $this->user = $user;
        $this->event = $event;
        $this->selectedTeamId = $selectedTeamId;
        $this->isCorrect = $isCorrect;
    }
}
