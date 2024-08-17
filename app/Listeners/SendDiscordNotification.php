<?php

namespace App\Listeners;

use App\Events\NflNewsStored;
use Spatie\DiscordAlerts\Facades\DiscordAlert;
use Illuminate\Support\Facades\Log;

class SendDiscordNotification
{
    public function handle(NflNewsStored $event)
    {
        $news = $event->nflNews;

        DiscordAlert::to('nfl-news')->message('', [
            [
                'title' => $news->headline,
                'description' => $news->description,
                'url' => $news->url,
                'published' => $news->published,
                'color' => '#7289da',
            ]
        ]);

        Log::info('Sent Discord notification for new news item', ['headline' => $news->headline, 'url' => $news->url]);
    }
}
