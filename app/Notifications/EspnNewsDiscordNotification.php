<?php

namespace App\Notifications;

use App\Helpers\DiscordHelper;
use App\Models\NflEspnNews;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class EspnNewsDiscordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newsId;

    /**
     * Create a new notification instance.
     *
     * @param int $newsId
     */
    public function __construct(int $newsId)
    {
        $this->newsId = $newsId;
    }

    /**
     * Get the notification's delivery channels.
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
     * @return DiscordMessage|null
     */
    public function toDiscord(mixed $notifiable): ?DiscordMessage
    {
        // Fetch the news model from the database
        $news = NflEspnNews::find($this->newsId);

        if (!$news) {
            return null; // If news not found, do not send notification
        }

        $color = $news->team ? $news->team->color : '#7289da'; // Default to Discord's blurple color

        $discordHelper = new DiscordHelper();
        return $discordHelper
            ->setTitle(":newspaper: {$news->headline}")
            ->setDescription($news->description)
            ->setUrl($news->url)
            ->addField('Byline', $news->byline ?? 'Unknown')
            ->setFooter("Published: {$news->published}")
            ->setColor($color)
            ->build();
    }
}
