<?php

namespace App\Jobs;

use App\Models\NflNews;
use App\Models\NflPlayer;

// Ensure you have this model to match player_id to team_id
use App\Services\NFLStatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchNflNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $playerID;
    protected $topNews;
    protected $fantasyNews;
    protected $recentNews;
    protected $maxItems;

    public function __construct($playerID, $topNews, $fantasyNews, $recentNews, $maxItems)
    {
        $this->playerID = $playerID;
        $this->topNews = $topNews;
        $this->fantasyNews = $fantasyNews;
        $this->recentNews = $recentNews;
        $this->maxItems = $maxItems;
    }

    public function handle(NFLStatsService $nflStatsService)
    {
        Log::info('Fetching NFL news with parameters', [
            'playerID' => $this->playerID,
            'topNews' => $this->topNews,
            'fantasyNews' => $this->fantasyNews,
            'recentNews' => $this->recentNews,
            'maxItems' => $this->maxItems,
        ]);

        $response = $nflStatsService->getNFLNews($this->playerID, $this->topNews, $this->fantasyNews, $this->recentNews, $this->maxItems);
        $newsItems = $response['body'] ?? [];

        if (empty($newsItems)) {
            Log::info('No news data found.');
            return;
        }

        foreach ($newsItems as $newsItem) {
            if (isset($newsItem['link'], $newsItem['title'], $newsItem['playerIDs'])) {
                $playerID = $newsItem['playerIDs'][0]; // Assuming we take the first playerID if multiple exist
                $teamID = $this->getTeamIdFromPlayerId($playerID);

                Log::info('Extracted player ID and team ID', ['playerID' => $playerID, 'teamID' => $teamID, 'title' => $newsItem['title']]);

                NflNews::updateOrCreate(
                    ['link' => $newsItem['link']],
                    [
                        'title' => $newsItem['title'],
                        'player_id' => $playerID,
                        'team_id' => $teamID,
                    ]
                );

                Log::info('News stored', ['title' => $newsItem['title'], 'player_id' => $playerID, 'team_id' => $teamID]);
            }
        }

        Log::info('NFL news fetched and stored successfully.');
    }

    private function getTeamIdFromPlayerId($playerID)
    {
        $player = NflPlayer::find($playerID);
        if ($player) {
            $teamID = $player->team_id;
            Log::info('Found team ID for player ID', ['teamID' => $teamID, 'playerID' => $playerID]);
            return $teamID;
        }
        Log::info('No team found for player ID', ['playerID' => $playerID]);
        return null;
    }
}
