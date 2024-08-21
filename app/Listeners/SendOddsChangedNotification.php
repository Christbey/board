<?php

namespace App\Listeners;

use App\Events\OddsChanged;
use App\Helpers\DiscordHelper;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\User;
use App\Notifications\DiscordNotification;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendOddsChangedNotification
{
    public function handle(OddsChanged $event)
    {
        try {
            $message = (new DiscordHelper())
                ->setTitle("{$event->homeTeam->name} vs {$event->awayTeam->name}")
                ->setDescription($event->messageText)
                ->setColor(null, $event->homeTeam)
                ->build();

            SendDiscordNotificationJob::dispatch($message, config('discord.default_channel'));
        } catch (Exception $e) {
            // Handle exception
            $messageText = 'An error occurred while calculating the spread.';
        }
    }
}
