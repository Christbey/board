<?php

namespace App\Jobs;

use App\Events\NflNewsFetched;
use App\Models\NflEspnAthlete;
use App\Models\NflEspnNews;
use App\Models\NflEspnTeam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

use App\Notifications\EspnNewsDiscordNotification;
use Illuminate\Support\Facades\Notification;

class FetchNflNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Log::info('Hunting ESPN');

        // Define the cache key and duration (e.g., 30 minutes)
        $cacheKey = 'nfl_news';
        $cacheDuration = 1800; // in seconds (30 minutes)

        // Attempt to retrieve news from the cache
        $newsItems = Cache::remember($cacheKey, $cacheDuration, function () {
            $response = Http::get('https://site.api.espn.com/apis/site/v2/sports/football/nfl/news?limit=10');

            if ($response->failed()) {
                Log::error('Failed to fetch news from ESPN', ['response' => $response->body()]);
                return []; // Return an empty array on failure
            }

            return $response->json('articles');
        });

        // If no news items are fetched or cached, log the issue and exit
        if (empty($newsItems)) {
            Log::warning('No NFL news items fetched or found in cache.');
            return;
        }

        // Process each news item and send the notification
        foreach ($newsItems as $newsItem) {
            $this->processNewsItem((array)$newsItem); // Cast stdClass to array
        }

        Log::info('Hunt Complete');
    }

    protected function processNewsItem(array $newsItem)
    {
        $teamId = $this->getValidTeamId($newsItem);
        $teamColor = NflEspnTeam::where('team_id', $teamId)->value('color') ?? '#7289da';

        $newsData = [
            'headline' => $newsItem['headline'] ?? null,
            'description' => $newsItem['description'] ?? null,
            'url' => $newsItem['links']['web']['href'] ?? null,
            'image_url' => $newsItem['images'][0]['url'] ?? null,
            'byline' => $newsItem['byline'] ?? null,
            'published' => isset($newsItem['published']) ? Carbon::parse($newsItem['published'])->toDateTimeString() : null,
            'last_modified' => isset($newsItem['lastModified']) ? Carbon::parse($newsItem['lastModified'])->toDateTimeString() : null,
            'team_id' => $teamId,
            'athlete_id' => $this->getValidAthleteId($newsItem),
        ];

        // Check if the news item already exists
        $exists = NflEspnNews::where('url', $newsData['url'])->exists();

        if (!$exists) {
            // Create a new news item
            $nflNews = NflEspnNews::create($newsData);
            Log::info("News item created: {$newsData['headline']}");

            // Send the notification
            Notification::route('discord', config('discord.nfl_news_channel'))
                ->notify(new EspnNewsDiscordNotification($nflNews->id));
        }
    }

    protected function getValidTeamId(array $newsItem): ?int
    {
        foreach ($newsItem['categories'] ?? [] as $category) {
            if ($category['type'] === 'team' && isset($category['teamId']) && NflEspnTeam::where('team_id', $category['teamId'])->exists()) {
                return $category['teamId'];
            }
        }
        return null;
    }

    protected function getValidAthleteId(array $newsItem): ?int
    {
        foreach ($newsItem['categories'] ?? [] as $category) {
            if ($category['type'] === 'athlete' && isset($category['athleteId']) && NflEspnAthlete::where('athlete_id', $category['athleteId'])->exists()) {
                return $category['athleteId'];
            }
        }
        return null;
    }
}
