<?php

namespace App\Listeners;

use App\Events\NflNewsFetched;
use App\Models\NflEspnAthlete;
use App\Models\NflEspnNews;
use App\Models\NflEspnTeam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class ProcessNflNews
{
    public function handle(NflNewsFetched $event)
    {
        $newsItem = $event->newsItem;

        $headline = $newsItem['headline'] ?? null;
        $description = $newsItem['description'] ?? null;
        $url = $newsItem['links']['web']['href'] ?? null;
        $imageUrl = $newsItem['images'][0]['url'] ?? null;
        $byline = $newsItem['byline'] ?? null;
        $published = isset($newsItem['published']) ? Carbon::parse($newsItem['published'])->toDateTimeString() : null;
        $lastModified = isset($newsItem['lastModified']) ? Carbon::parse($newsItem['lastModified'])->toDateTimeString() : null;

        $teamId = null;
        $athleteId = null;

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

        if ($athleteId && !NflEspnAthlete::where('athlete_id', $athleteId)->exists()) {
            Log::warning('Athlete ID does not exist in the nfl_espn_athletes table', ['athleteId' => $athleteId]);
            $athleteId = null;
        }

        if ($teamId && !NflEspnTeam::where('team_id', $teamId)->exists()) {
            Log::warning('Team ID does not exist in the nfl_espn_teams table', ['teamId' => $teamId]);
            $teamId = null;
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

        // Send Discord notification only if the record was recently created
        if ($nflNews->wasRecentlyCreated) {
            DiscordAlert::to('nfl-news')->message('', [
                [
                    'title' => $headline,
                    'description' => $description,
                    'url' => $url,
                    'published' => $published,
                    'color' => '#7289da',
                ]
            ]);
            Log::info('Sent Discord notification for new news item', ['headline' => $headline, 'url' => $url]);
        }

        Log::info('News stored', ['headline' => $headline, 'url' => $url]);
    }
}
