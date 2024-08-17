<?php

namespace App\Console\Commands\Espn;

use App\Models\NflEspnAthlete;
use App\Models\NflEspnNews;
use App\Models\NflEspnTeam;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

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
            Log::info('Processing news item', ['newsItem' => $newsItem]);

            $headline = $newsItem['headline'] ?? null;
            $description = $newsItem['description'] ?? null;
            $url = $newsItem['links']['web']['href'] ?? null;
            $imageUrl = $newsItem['images'][0]['url'] ?? null;
            $byline = $newsItem['byline'] ?? null;
            $published = isset($newsItem['published']) ? Carbon::parse($newsItem['published'])->toDateTimeString() : null;
            $lastModified = isset($newsItem['lastModified']) ? Carbon::parse($newsItem['lastModified'])->toDateTimeString() : null;

            $teamId = null;
            $athleteId = null;

            // Extract team_id and athlete_id from the categories array
            if (isset($newsItem['categories'])) {
                foreach ($newsItem['categories'] as $category) {
                    if ($category['type'] === 'athlete' && isset($category['athleteId'])) {
                        $athleteId = $category['athleteId'];
                    }

                    if ($category['type'] === 'team' && isset($category['teamId'])) {
                        $teamId = $category['teamId'];
                    }
                }
            }

            // Check if athlete_id and team_id exist in their respective table.
            if ($athleteId && !NflEspnAthlete::where('athlete_id', $athleteId)->exists()) {
                Log::warning('Athlete ID does not exist in the nfl_espn_athletes table', ['athleteId' => $athleteId]);
                $athleteId = null; // Set to null to avoid foreign key constraint violation
            }

            if ($teamId && !NflEspnTeam::where('team_id', $teamId)->exists()) {
                Log::warning('Team ID does not exist in the nfl_espn_teams table', ['teamId' => $teamId]);
                $teamId = null; // Set to null to avoid foreign key constraint violation
            }

            Log::info('Extracted IDs', ['teamId' => $teamId, 'athleteId' => $athleteId]);

            $nflNews = NflEspnNews::updateOrCreate(
                ['url' => $url],
                [
                    'headline' => $headline,
                    'description' => $description,
                    'url' => $url,
                    'image_url' => $imageUrl,
                    'byline' => $byline,
                    'published' => $published,
                    'last_modified' => $lastModified,
                    'team_id' => $teamId,
                    'athlete_id' => $athleteId,
                ]
            );

            // Only send a Discord notification if the record was recently created
            if ($nflNews->wasRecentlyCreated) {
                DiscordAlert::to('nfl-news')->message('', [
                    [
                        'title' => $headline,
                        'description' => $description,
                        'url' => $url,
                        'published' => $published,
                        'color' => '#7289da', // Optional: You can specify a color for the embed (hex code or decimal)
                    ]
                ]);
                Log::info('Sent Discord notification for new news item', ['headline' => $headline, 'url' => $url]);
            }

            Log::info('News stored', ['headline' => $headline, 'url' => $url]);
        }

        Log::info('NFL news fetched and stored successfully');
    }
}
