<?php

namespace App\Observers;

use App\Models\NflEspnEvent;
use App\Helpers\DiscordHelper;
use App\Models\User;
use App\Notifications\DiscordNotification;
use Illuminate\Support\Facades\Log;

class NflEspnEventObserver
{
    public function updated(NflEspnEvent $event)
    {
        // Retrieve related team names, scores, and colors using the team IDs
        $awayTeam = $event->awayTeam;
        $homeTeam = $event->homeTeam;

        $awayTeamName = $awayTeam->name ?? 'Away Team';
        $homeTeamName = $homeTeam->name ?? 'Home Team';

        $awayTeamScore = $event->away_team_score ?? 'N/A';
        $homeTeamScore = $event->home_team_score ?? 'N/A';

        $statusDetail = $event->status_type_detail ?? 'N/A';

        // Determine the color based on the higher score
        if ($awayTeamScore > $homeTeamScore) {
            $color = $awayTeam->color ?? '#7289da'; // Default color if not set
        } else {
            $color = $homeTeam->color ?? '#7289da'; // Default color if not set
        }

        // Format the description
        $description = "{$statusDetail}";
        $title = "{$awayTeamName} {$awayTeamScore} at {$homeTeamName} {$homeTeamScore}";

        try {
            // Build the Discord message
            $message = (new DiscordHelper())
                ->setTitle($title)
                ->setDescription($description)
                ->setColor($color)
                ->build();

            // Notify via Discord
            $user = User::find(1); // Replace with your logic to determine the user to notify
            if ($user) {
                $user->notify(new DiscordNotification($message));
            }

            Log::info('Discord notification sent for NflEspnEvent update');
        } catch (Exception $e) {
            Log::error('Failed to send Discord notification', ['error' => $e->getMessage()]);
        }
    }
}
