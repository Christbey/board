<?php

namespace App;

use Illuminate\Notifications\Notifiable;

class DiscordNotifier
{
    use Notifiable;

    protected $webhookKey;

    public function __construct($webhookKey)
    {
        $this->webhookKey = $webhookKey;
    }

    public function routeNotificationForDiscord()
    {
        return config("services.discord.{$this->webhookKey}");
    }
}

