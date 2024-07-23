<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Elo\EloRatingSystem;
use App\Models\NflTeamSchedule;
use App\Models\NflStadium;

class UpdateEloRatings extends Command
{
    protected $signature = 'elo:update';
    protected $description = 'Update Elo ratings and calculate EPA based on the latest NFL game results';

    protected EloRatingSystem $eloRatingSystem;

    public function __construct(EloRatingSystem $eloRatingSystem)
    {
        parent::__construct();
        $this->eloRatingSystem = $eloRatingSystem;
    }

    public function handle()
    {
        $this->info('Starting Elo ratings update...');

        // Train Elo ratings with past seasons
        $this->eloRatingSystem->trainRatingsWithPastSeasons();

        // Get future games that are scheduled
        $futureGames = NflTeamSchedule::whereNull('home_result')
            ->whereNull('away_result')
            ->where('game_status', 'scheduled')
            ->get();

        // Fetch all stadiums to minimize queries
        $stadiums = NflStadium::whereIn('team_id', $futureGames->pluck('team_id_home')->merge($futureGames->pluck('team_id_away'))->unique())->get()->keyBy('team_id');

        // Log expected winning percentages and predicted scores
        $this->info('Logging expected winning percentages and predicted scores for future games...');
        foreach ($futureGames as $game) {
            $homeStadium = $stadiums->get($game->team_id_home);
            $awayStadium = $stadiums->get($game->team_id_away);

            $this->eloRatingSystem->logExpectedWinningPercentageAndPredictedScore($game, $homeStadium, $awayStadium);
        }

        // Get updated Elo ratings
        $ratings = $this->eloRatingSystem->getRatings();

        $this->info('Updated Elo ratings:');
        foreach ($ratings as $team => $rating) {
            $this->line("Team ID {$team}: {$rating}");
        }

        // Calculate expected wins for teams
        $expectedWins = $this->eloRatingSystem->calculateExpectedWinsForTeams();

        $this->info("\nExpected Wins:");
        foreach ($expectedWins as $teamId => $wins) {
            $this->line("Team ID {$teamId}: " . round($wins, 2) . ' expected wins');
        }

        $this->info('Elo ratings update completed.');
    }
}
