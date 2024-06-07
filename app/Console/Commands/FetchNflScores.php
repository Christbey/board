<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchNflScores extends Command
{
    protected $signature = 'fetch:nfl-scores';
    protected $description = 'Fetch NFL scores and store them in the database';

    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        parent::__construct();

        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function handle()
    {
        try {
            $response = Http::get("{$this->baseUrl}/sports/americanfootball_nfl/scores", [
                'apiKey' => $this->apiKey,
                'daysFrom' => 3,
                'dateFormat' => 'iso',
            ]);

            if ($response->successful()) {
                $scores = $response->json();
                $this->storeScores($scores);
                $this->info('NFL scores fetched and stored successfully.');
            } else {
                $this->error('Failed to fetch NFL scores.');
                Log::error('Failed to fetch NFL scores: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Error fetching NFL scores.');
            Log::error('Error fetching NFL scores: ' . $e->getMessage());
        }
    }

    protected function storeScores(array $scoresData)
    {
        foreach ($scoresData as $score) {
            $homeTeam = \App\Models\NflTeam::firstOrCreate(['name' => $score['home_team']]);
            $awayTeam = \App\Models\NflTeam::firstOrCreate(['name' => $score['away_team']]);

            if ($homeTeam && $awayTeam) {
                \App\Models\NflScore::updateOrCreate(
                    ['event_id' => $score['id']],
                    [
                        'sport_key' => $score['sport_key'],
                        'sport_title' => $score['sport_title'],
                        'commence_time' => \Carbon\Carbon::parse($score['commence_time'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s'),
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $awayTeam->id,
                        'home_team_score' => $score['scores'][0]['score'] ?? null,
                        'away_team_score' => $score['scores'][1]['score'] ?? null,
                        'last_update' => isset($score['last_update']) ? \Carbon\Carbon::parse($score['last_update'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s') : null,
                    ]
                );
            }
        }
    }
}
