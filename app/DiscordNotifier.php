<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;

class DiscordNotifier
{
    use Notifiable;

    protected $channelId;

    public function __construct($channelKey = null)
    {
        // If a channelKey is provided, fetch the corresponding channel ID from the config
        $this->channelId = $channelKey ? Config::get("discord.{$channelKey}") : null;
    }

    public function routeNotificationForDiscord()
    {
        return $this->channelId;
    }
}
