<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Events\NflNewsFetched;

class FetchNflNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info('Starting to fetch NFL news from ESPN');

        $response = Http::get('https://site.api.espn.com/apis/site/v2/sports/football/nfl/news?limit=5');

        if ($response->failed()) {
            Log::error('Failed to fetch news from ESPN', ['response' => $response->body()]);
            return;
        }

        $newsItems = $response->json('articles');
        // Log::info('Fetched news items', ['newsItems' => $newsItems]);

        foreach ($newsItems as $newsItem) {
            event(new NflNewsFetched($newsItem));
        }

        Log::info('NFL news fetched and stored successfully');
    }
}
