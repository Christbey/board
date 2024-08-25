<?php

namespace App;

use Illuminate\Support\Facades\Notification;

class DiscordNotifier
{
    protected $channel;

    public function __construct($channel)
    {
        $this->channel = $channel;
    }

    public function notify($notifiable, $notification)
    {
        // Ensure the Notification facade is used to send the notification
        Notification::route('discord', config("discord.{$this->channel}"))
            ->notify($notification);
    }
}
