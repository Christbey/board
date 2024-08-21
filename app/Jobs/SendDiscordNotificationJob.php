<?php

namespace App\Jobs;

use App\Notifications\DiscordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendDiscordNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $channelId;

    /**
     * Create a new job instance.
     *
     * @param mixed $message
     * @param string|null $channelId
     */
    public function __construct($message, string $channelId = null)
    {
        $this->message = $message;
        $this->channelId = $channelId ?? config('discord.default_channel_id'); // Default to a channel ID from config
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Build the Discord message
        $discordMessage = $this->message;

        // Send the notification to the specified Discord channel
        Notification::route('discord', $this->channelId)->notify(new DiscordNotification($discordMessage));
    }
}
