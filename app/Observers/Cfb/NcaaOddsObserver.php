<?php

namespace App\Observers\Cfb;

use App\Models\NcaaOdds;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CfbPredictionNotification;

class NcaaOddsObserver
{
    public function updated(NcaaOdds $odds)
    {
        // Send notification whenever the NcaaOdds model is updated
        Notification::route('discord', config('discord.cfb_odds_channel'))
            ->notify(new CfbPredictionNotification($odds));
    }
}
