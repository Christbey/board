<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DiscordMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;

class OddsUpdateNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {
        return (new DiscordMessage)
            ->content($this->message);
    }
}
