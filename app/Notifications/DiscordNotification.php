<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $discordMessage;

    /**
     * DiscordNotification constructor.
     *
     * @param DiscordMessage $discordMessage
     */
    public function __construct(DiscordMessage $discordMessage)
    {
        $this->discordMessage = $discordMessage;
    }

    /**
     * Determine the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    /**
     * Get the Discord representation of the notification.
     *
     * @param mixed $notifiable
     * @return DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        return $this->discordMessage;
    }
}
