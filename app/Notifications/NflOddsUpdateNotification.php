<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use App\Helpers\DiscordHelper;

class NflOddsUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $odds;

    public function __construct($odds)
    {
        $this->odds = $odds;
    }

    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    public function toDiscord(mixed $notifiable): DiscordMessage
    {
        $homeImpliedWinningPercentage = round($this->calculateImpliedWinningPercentage($this->odds->h2h_home_price), 2);
        $awayImpliedWinningPercentage = round($this->calculateImpliedWinningPercentage($this->odds->h2h_away_price), 2);
        $impliedTotal = $this->calculateImpliedTotal();

        $favoriteTeam = $this->odds->h2h_home_price < $this->odds->h2h_away_price ? $this->odds->homeTeam : $this->odds->awayTeam;
        $favoriteSpread = $this->odds->h2h_home_price < $this->odds->h2h_away_price ? $this->odds->spread_home_point : $this->odds->spread_away_point;

        $description = ":star2: **{$favoriteTeam->name}** is favored by **{$favoriteSpread}**\n";

        // Handle 0 values
        $homeWP = $homeImpliedWinningPercentage ?: 'N/A';
        $awayWP = $awayImpliedWinningPercentage ?: 'N/A';
        $total = $impliedTotal ?: 'N/A';

        $discordHelper = new DiscordHelper();

        // Start building the Discord message
        $discordHelper = new DiscordHelper();

        $discordHelper = $discordHelper
            ->setTitle(':football: **NFL Odds Update**')
            ->setDescription($description);

        if ($homeWP !== 'N/A') {
            $discordHelper = $discordHelper->addField(":house: **{$this->odds->homeTeam->name}**", "Implied WP: **{$homeWP}%**\n", true);
        }

        if ($awayWP !== 'N/A') {
            $discordHelper = $discordHelper->addField(":airplane: **{$this->odds->awayTeam->name}**", "Implied WP: **{$awayWP}%**\n", true);
        }

        if ($total !== 'N/A') {
            $discordHelper = $discordHelper->addField(':scales: **Implied Total**', "**{$total}**\n", false);
        }

        // Ensure commence_time is treated as a Carbon instance
        $commenceTime = Carbon::parse($this->odds->commence_time);
        $footerText = "Odds provided by {$this->odds->bookmaker_key} | " . $commenceTime->format('Y-m-d H:i:s');

        return $discordHelper
            ->setFooter($footerText)
            ->setColor($favoriteTeam->primary_color) // Use the favorite team's primary color
            ->build();
    }

    protected function calculateImpliedWinningPercentage($price)
    {
        return $price > 0 ? 100 / ($price + 100) * 100 : abs($price) / (abs($price) + 100) * 100;
    }

    protected function calculateImpliedTotal()
    {
        return ($this->odds->total_over_point + $this->odds->total_under_point) / 2;
    }
}
