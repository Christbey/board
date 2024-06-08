<?php

// app/Console/Commands/FetchMLBScores.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\MlbScore;
use App\Models\MlbTeam;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchMLBScores extends Command
{
    protected $signature = 'mlb:scores:fetch';
    protected $description = 'Fetch the latest MLB scores from the API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $response = Http::get(env('ODDS_API_BASE_URL') . '/sports/baseball_mlb/scores', [
            'apiKey' => env('ODDS_API_KEY'),
            'daysFrom' => 3,
            'dateFormat' => 'iso'
        ]);

        // Log the response for debugging purposes
        Log::info('MLB Scores API Response', ['response' => $response->json()]);

        if ($response->successful()) {
            $scores = $response->json();

            foreach ($scores as $score) {
                // Parse and convert the commence_time to CST and format it
                $commenceTime = Carbon::parse($score['commence_time'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s');

                // Initialize scores
                $homeTeamScore = null;
                $awayTeamScore = null;

                // Check if the scores key exists and is an array
                if (isset($score['scores']) && is_array($score['scores'])) {
                    foreach ($score['scores'] as $teamScore) {
                        if ($teamScore['name'] === $score['home_team']) {
                            $homeTeamScore = $teamScore['score'];
                        } elseif ($teamScore['name'] === $score['away_team']) {
                            $awayTeamScore = $teamScore['score'];
                        }
                    }
                }

                MlbScore::updateOrCreate(
                    ['event_id' => $score['id']],
                    [
                        'sport_key' => $score['sport_key'],
                        'sport_title' => $score['sport_title'],
                        'commence_time' => $commenceTime,
                        'completed' => $score['completed'],
                        'home_team_id' => MlbTeam::firstOrCreate(['name' => $score['home_team']])->id,
                        'away_team_id' => MlbTeam::firstOrCreate(['name' => $score['away_team']])->id,
                        'home_team_score' => $homeTeamScore,
                        'away_team_score' => $awayTeamScore,
                        'last_update' => now(),
                    ]
                );
            }

            $this->info('MLB scores fetched and stored in the database.');
        } else {
            $this->error('Failed to fetch MLB scores: ' . $response->body());
            Log::error('Failed to fetch MLB scores: ' . $response->body());
        }

        return 0;
    }
}
