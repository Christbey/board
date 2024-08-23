<?php

namespace App\Console\Commands\Cfb\Game;

use App\Models\CollegeFootballPregame;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Spatie\DiscordAlerts\Facades\DiscordAlert;
use Illuminate\Support\Facades\Log;

class FetchCollegeFootballPregame extends Command
{
    protected $signature = 'fetch:college-football-pregame {year=2024} {week=1}';
    protected $description = 'Fetch college football pregame win probability data from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $week = $this->argument('week');

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('collegefootball.api_key'),
        ])->get("https://api.collegefootballdata.com/metrics/wp/pregame?year={$year}&week={$week}");

        if ($response->successful()) {
            $games = $response->json();

            foreach ($games as $game) {
                // Find the corresponding teams by name
                $homeTeam = CollegeFootballTeam::where('school', $game['homeTeam'])->first();
                $awayTeam = CollegeFootballTeam::where('school', $game['awayTeam'])->first();

                if (!$homeTeam || !$awayTeam) {
                    $this->error('Team not found for game: ' . $game['gameId']);
                    continue;
                }

                // Find the existing pregame data
                $pregame = CollegeFootballPregame::where('game_id', $game['gameId'])->first();

                // Determine if home_win_prob has changed before updating the record
                $homeWinProbChanged = $pregame ? round($pregame->home_win_prob, 2) !== round($game['homeWinProb'], 2) : true;

                // Send notification only if home_win_prob changed
                if ($homeWinProbChanged) {
                    $homeTeamName = $homeTeam->school;
                    $awayTeamName = $awayTeam->school;
                    $gameId = $game['gameId'];
                    $homeWinProb = $game['homeWinProb'];

                    // Send a Discord notification
                    /*                    DiscordAlert::to('cfb-events')->message('', [
                                            [
                                                'title' => "Pregame Updated: {$homeTeamName} vs {$awayTeamName}",
                                                'description' => "The home win probability for Game ID: {$gameId} has been updated.",
                                                'fields' => [
                                                    [
                                                        'name' => 'Home Win Probability',
                                                        'value' => "{$homeWinProb}%",
                                                        'inline' => true,
                                                    ],
                                                    [
                                                        'name' => 'Spread',
                                                        'value' => "{$game['spread']}",
                                                        'inline' => true,
                                                    ]
                                                ],
                                                'color' => '#7289da', // Discord blue color, will be converted to decimal automatically
                                                'timestamp' => now()->toIso8601String(),
                                            ]
                                        ]);*/

                    Log::info('Sent Discord notification for pregame update', [
                        'game_id' => $gameId,
                        'home_team' => $homeTeamName,
                        'away_team' => $awayTeamName,
                        'home_win_prob' => $homeWinProb,
                    ]);
                }

                // Perform the updateOrCreate operation
                CollegeFootballPregame::updateOrCreate(
                    [
                        'game_id' => $game['gameId']
                    ],
                    [
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $awayTeam->id,
                        'spread' => $game['spread'],
                        'home_win_prob' => $game['homeWinProb'],
                        'season_type' => $game['seasonType'],
                        'season' => $game['season'],
                        'week' => $game['week'],
                    ]
                );
            }

            $this->info("College football pregame win probability data for year {$year}, week {$week} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
