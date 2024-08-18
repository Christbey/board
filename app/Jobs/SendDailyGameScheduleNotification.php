<?php

namespace App\Jobs;

use App\Models\NflEspnEvent;
use App\Helpers\DiscordHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Log;

class SendDailyGameScheduleNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Get today's date
        $today = now()->toDateString();

        // Fetch all games scheduled for today
        $games = NflEspnEvent::with(['homeTeam', 'awayTeam'])
            ->whereDate('date', $today)
            ->get();

        if ($games->isEmpty()) {
            Log::info('No games scheduled for today.');
            return;
        }

        // Send the notification
        Notification::route('discord', config('discord.webhook_url'))
            ->notify(new DiscordDailyGameScheduleNotification($games));
    }
}
