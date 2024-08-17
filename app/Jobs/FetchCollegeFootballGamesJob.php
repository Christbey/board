<?php

namespace App\Jobs;

use App\Services\CollegeFootballApiService;
use App\Traits\UpdatesCollegeFootballGames;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class FetchCollegeFootballGamesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, UpdatesCollegeFootballGames;

    protected $year;
    protected $seasonType;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($year, $seasonType)
    {
        $this->year = $year;
        $this->seasonType = $seasonType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CollegeFootballApiService $service)
    {
        $apiBaseUrl = config('collegefootball.api_base_url');
        $url = "{$apiBaseUrl}/games?year={$this->year}&seasonType={$this->seasonType}";

        $response = $service->fetchFromApi($url);

        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                $homeTeam = $service->findTeam($game['home_team']);
                $awayTeam = $service->findTeam($game['away_team']);
                $homeConference = $service->findConference($game['home_conference']);
                $awayConference = $service->findConference($game['away_conference']);

                $updatedGame = $this->updateOrCreateCollegeFootballGame($game, $homeTeam, $awayTeam, $homeConference, $awayConference);

                // Check if the home_line_scores field was updated and send a notification


                if ($updatedGame->wasChanged('home_line_scores')) {
                    $homeTeam = $updatedGame->home_team;
                    $awayTeam = $updatedGame->away_team;
                    $gameId = $updatedGame->id;
                    $updatedScores = json_encode($updatedGame->home_line_scores);

                    DiscordAlert::to('cfb-events')->message('', [
                        [
                            'title' => "{$homeTeam} vs {$awayTeam}",
                            'description' => "The home_line_scores for Game ID: {$gameId} have been updated.",
                            'fields' => [
                                [
                                    'name' => 'Updated home_line_scores',
                                    'value' => $updatedScores,
                                    'inline' => true,
                                ],
                            ],
                            'color' => '#7289da', // Discord blue color, will be converted to decimal automatically
                            'timestamp' => now()->toIso8601String(),
                        ]
                    ]);

                    Log::info('Sent Discord notification for game update', [
                        'game_id' => $gameId,
                        'home_team' => $homeTeam,
                        'away_team' => $awayTeam,
                        'updated_scores' => $updatedScores,
                    ]);
                }

            }
        } else {
            Log::error('Failed to fetch data from the API.');
        }
    }
}
