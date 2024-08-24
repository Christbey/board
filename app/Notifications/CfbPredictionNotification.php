<?php

namespace App\Notifications;

use App\Helpers\DiscordHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballPregame;
use Carbon\Carbon;

class CfbPredictionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $game;

    public function __construct(CollegeFootballGame $game)
    {
        $this->game = $game;
    }

    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {
        $homeFpi = CollegeFootballFpiRating::where('team_id', $this->game->home_team_id)
            ->where('year', $this->game->season)
            ->first();
        $awayFpi = CollegeFootballFpiRating::where('team_id', $this->game->away_team_id)
            ->where('year', $this->game->season)
            ->first();
        $pregameData = CollegeFootballPregame::where('game_id', $this->game->id)->first();

        if (!$homeFpi || !$awayFpi || !$pregameData) {
            $discordHelper = new DiscordHelper();
            return $discordHelper
                ->setTitle(':football: **College Football Prediction**')
                ->setDescription('FPI ratings or pregame data not found for one or both teams')
                ->build();
        }

        // Calculate the prediction with Elo and Pregame adjustments
        $homeAdvantage = $this->calculateHomeAdvantage($this->game);
        $eloImpact = $this->calculateEloImpact($this->game);
        $spreadImpact = $this->calculateSpreadImpact($pregameData);

        $homeScore = $homeFpi->fpi + $homeAdvantage + $eloImpact['home'] + $spreadImpact['home'];
        $awayScore = $awayFpi->fpi + $eloImpact['away'] + $spreadImpact['away'];

        $predictedWinner = $homeScore > $awayScore ? $this->game->homeTeam->school : $this->game->awayTeam->school;

        $discordHelper = new DiscordHelper();

        $discordHelper = $discordHelper
            ->setTitle(':football: **College Football Prediction**')
            ->addField(':house: **' . $this->game->homeTeam->school . '**', 'Predicted Score: **' . round($homeScore, 2) . '**', true)
            ->addField(':airplane: **' . $this->game->awayTeam->school . '**', 'Predicted Score: **' . round($awayScore, 2) . '**', true)
            ->addField(':trophy: **Predicted Winner**', '**' . $predictedWinner . '**', false)
            ->setFooter('Game scheduled for ' . Carbon::parse($this->game->commence_time)->format('l, F j, Y \a\t g:i A'));

        return $discordHelper->build();
    }

    private function calculateHomeAdvantage(CollegeFootballGame $game): float
    {
        return $game->neutral_site ? 0 : 2.5;
    }

    private function calculateEloImpact(CollegeFootballGame $game): array
    {
        $eloDifference = $game->home_pregame_elo - $game->away_pregame_elo;
        $scalingFactor = 0.01;

        return [
            'home' => $eloDifference * $scalingFactor,
            'away' => -$eloDifference * $scalingFactor,
        ];
    }

    private function calculateSpreadImpact(CollegeFootballPregame $pregameData): array
    {
        $spreadScalingFactor = 0.5;

        return [
            'home' => $pregameData->spread * $spreadScalingFactor,
            'away' => -$pregameData->spread * $spreadScalingFactor,
        ];
    }
}
