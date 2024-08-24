<?php

namespace App\Observers\Cfb;

use App\Models\CollegeFootballGame;
use App\Services\CfbPrediction\PredictionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CfbPredictionNotification;

class CollegeFootballGameObserver
{
    protected $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    public function updated(CollegeFootballGame $game)
    {
        // Send a Discord notification with the updated prediction
        Notification::route('discord', config('discord.cfb_odds_channel'))
            ->notify(new CfbPredictionNotification($game));
    }
}
