<?php

namespace App\Listeners;

use App\Events\OddsChanged;
use App\Helpers\DiscordHelper;
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

            $user = User::find(1); // Retrieve the user to send the notification
            $user?->notify(new DiscordNotification($message));

            Log::info('Notification sent to Discord successfully.');
        } catch (Exception $e) {
            Log::error('Failed to send notification to Discord', ['error' => $e->getMessage()]);
        }
    }
}
