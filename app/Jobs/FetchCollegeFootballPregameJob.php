<?php

namespace App\Jobs;

use App\Models\CollegeFootballPregame;
use App\Models\CollegeFootballTeam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\DiscordAlerts\Facades\DiscordAlert;
use Illuminate\Support\Facades\Log;

class FetchCollegeFootballPregameJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $game;
    protected $homeTeam;
    protected $awayTeam;

    /**
     * Create a new job instance.
     */
    public function __construct($game, $homeTeam, $awayTeam)
    {
        $this->game = $game;
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $pregame = CollegeFootballPregame::where('game_id', $this->game['gameId'])->first();

        // Determine if home_win_prob has changed
        $homeWinProbChanged = false;

        if ($pregame) {
            $homeWinProbChanged = round($pregame->home_win_prob, 2) !== round($this->game['homeWinProb'], 2);
        }

        // Send notification if home_win_prob has changed
        if ($homeWinProbChanged) {
            $homeTeamName = $this->homeTeam->school;
            $awayTeamName = $this->awayTeam->school;
            $gameId = $this->game['gameId'];
            $homeWinProb = $this->game['homeWinProb'];

            // Send a Discord notification
            DiscordAlert::to('cfb-events')->message('', [
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
                            'value' => "{$this->game['spread']}",
                            'inline' => true,
                        ]
                    ],
                    'color' => '#7289da', // Discord blue color, will be converted to decimal automatically
                    'timestamp' => now()->toIso8601String(),
                ]
            ]);

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
                'game_id' => $this->game['gameId']
            ],
            [
                'home_team_id' => $this->homeTeam->id,
                'away_team_id' => $this->awayTeam->id,
                'spread' => $this->game['spread'],
                'home_win_prob' => $this->game['homeWinProb'],
                'season_type' => $this->game['seasonType'],
                'season' => $this->game['season'],
                'week' => $this->game['week'],
            ]
        );
    }
}
