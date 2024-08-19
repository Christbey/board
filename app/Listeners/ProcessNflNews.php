<?php

namespace App\Listeners;

use App\Events\NflNewsFetched;
use App\Helpers\DiscordHelper;
use App\Models\NflEspnAthlete;
use App\Models\NflEspnNews;
use App\Models\NflEspnTeam;
use App\Models\User;
use App\Notifications\DiscordNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessNflNews
{
    public function handle(NflNewsFetched $event)
    {
        $newsItem = $event->newsItem;

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

            $user = $this->getNotificationUser();

            if ($user) {
                $message = (new DiscordHelper())
                    ->presetEmbed(
                        $newsData['headline'] ?? 'News Update',
                        $newsData['description'] ?? 'Description unavailable.',
                        $newsData['url'] ?? '',
                        $newsData['byline'] ?? 'Unknown',
                        'NFL News',
                        $teamColor,
                        $newsData['published']
                    )
                    ->build();

                $user->notify(new DiscordNotification($message));
            }
        }
    }

    protected function getValidTeamId(array $newsItem)
    {
        foreach ($newsItem['categories'] ?? [] as $category) {
            if ($category['type'] === 'team' && isset($category['teamId']) && NflEspnTeam::where('team_id', $category['teamId'])->exists()) {
                return $category['teamId'];
            }
        }
        return null;
    }

    protected function getValidAthleteId(array $newsItem)
    {
        foreach ($newsItem['categories'] ?? [] as $category) {
            if ($category['type'] === 'athlete' && isset($category['athleteId']) && NflEspnAthlete::where('athlete_id', $category['athleteId'])->exists()) {
                return $category['athleteId'];
            }
        }
        return null;
    }

    protected function getNotificationUser()
    {
        return User::find(1); // Replace with your logic to determine the user to notify
    }
}
