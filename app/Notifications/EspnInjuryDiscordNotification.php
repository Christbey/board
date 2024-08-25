<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use App\Helpers\DiscordHelper;
use Illuminate\Support\Facades\Config;

class EspnInjuryDiscordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $injury;

    /**
     * Create a new notification instance.
     *
     * @param $injury
     */
    public function __construct($injury)
    {
        $this->injury = $injury;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class]; // Use the DiscordChannel class for notifications
    }

    /**
     * Get the Discord representation of the notification.
     *
     * @param mixed $notifiable
     * @return DiscordMessage
     */
    public function toDiscord(mixed $notifiable): DiscordMessage
    {
        $color = match ($this->injury->status) {
            'Active' => '#00FF00',  // Green
            'Questionable' => '#FFFF00',  // Yellow
            'Out', 'Injured Reserve' => '#FF0000',  // Red
            default => '#FFFFFF',  // Default to white if status doesn't match
        };

        $footerText = "{$this->injury->status} - {$this->injury->date}";

        $discordHelper = new DiscordHelper();
        return $discordHelper
            ->setTitle(":medical_symbol: {$this->injury->athlete->full_name} Injury Report")
            ->setDescription($this->injury->description)
            ->addField('Team', $this->injury->team->display_name)
            ->setFooter($footerText)
            ->setColor($color)
            ->sleep(1) // Sleep for 1 second before sending the notification
            ->build();
    }

}
