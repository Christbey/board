<?php

namespace App\Notifications;

use App\Helpers\DiscordHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use App\Models\NcaaOdds;

class CfbPredictionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected NcaaOdds $odds;

    public function __construct(NcaaOdds $odds)
    {
        $this->odds = $odds;
    }

    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {
        // Calculate home win probability from odds
        $homeWinProb = $this->odds->h2h_home_price > 0
            ? 100 / ($this->odds->h2h_home_price + 100)
            : -$this->odds->h2h_home_price / (-$this->odds->h2h_home_price + 100);
        $homeWinProb = round($homeWinProb * 100, 2); // Convert to percentage

        // Calculate away win probability (100% - home win probability)
        $awayWinProb = 100 - $homeWinProb;

        // Fetch the associated team names
        $homeTeamName = $this->odds->homeTeam->name;
        $awayTeamName = $this->odds->awayTeam->name;
        $total = $this->odds->total_over_point;

        // Determine the predicted winner and the corresponding win probability
        $predictedWinner = $homeWinProb > 50 ? $homeTeamName : $awayTeamName;
        $predictedWinnerProb = $homeWinProb > 50 ? $homeWinProb : $awayWinProb;
        $title = ":football: **$awayTeamName vs. $homeTeamName**";
        $commenceTime = $this->odds->commence_time;

        // Prepare and send the Discord notification
        return (new DiscordHelper())
            ->setTitle($title)
            ->addField($predictedWinner . ' Win Probability', '**' . $predictedWinnerProb . '%**', true)
            ->addfield('Total', $total, true)
            ->setFooter('Commence Time: ' . $commenceTime) // Add commence time to the footer
            ->build();
    }
}
