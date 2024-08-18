<?php

namespace App\Console\Commands;

use App\Jobs\QueryTodaysGames;
use App\Helpers\DiscordHelper;
use App\Notifications\DiscordNotification;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyGameScheduleNotification extends Command
{
    protected $signature = 'notify:daily-games';
    protected $description = 'Send a daily notification to Discord with today\'s NFL games';

    public function handle()
    {
        // Dispatch the job to query today's games
        $job = new QueryTodaysGames();
        $job->handle();

        // If there are games scheduled, send a notification
        if ($job->games->isNotEmpty()) {
            try {
                $title = "Today's NFL Games Schedule";
                $description = 'Here are the games scheduled for today:';
                $color = '#7289da'; // Default color, you can customize this

                // Use the DiscordHelper to build the message
                $discordHelper = new DiscordHelper();
                $discordHelper->setTitle($title)
                    ->setDescription($description)
                    ->setTimestamp(now());

                foreach ($job->games as $game) {
                    $gameDetails = "{$game->venue_name}";
                    $discordHelper->addField($game->name, $gameDetails, false);
                }

                $message = $discordHelper->setColor($color)->build();

                // Notify via Discord
                $user = User::find(1); // Replace with your logic to determine the user to notify
                $user?->notify(new DiscordNotification($message));

                Log::info('Discord notification sent for daily games schedule');
            } catch (Exception $e) {
                Log::error('Failed to send Discord notification', ['error' => $e->getMessage()]);
            }
        } else {
            $this->info('No games scheduled for today.');
        }

        return 0;
    }
}
