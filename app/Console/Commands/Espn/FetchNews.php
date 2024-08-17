<?php

namespace App\Console\Commands\Espn;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Events\NflNewsFetched;

class FetchNews extends Command
{
    protected $signature = 'espn:nfl-news';
    protected $description = 'Fetch and store NFL news from ESPN';

    public function handle()
    {
        Log::info('Starting to fetch NFL news from ESPN');

        $response = Http::get('https://site.api.espn.com/apis/site/v2/sports/football/nfl/news?limit=50');

        if ($response->failed()) {
            $this->error('Failed to fetch news from ESPN');
            Log::error('Failed to fetch news from ESPN', ['response' => $response->body()]);
            return;
        }

        $newsItems = $response->json('articles');
        Log::info('Fetched news items', ['newsItems' => $newsItems]);

        foreach ($newsItems as $newsItem) {
            event(new NflNewsFetched($newsItem));
        }

        Log::info('NFL news fetched and stored successfully');
    }
}
