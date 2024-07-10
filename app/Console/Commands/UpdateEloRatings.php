<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Elo\EloRatingSystem;
use App\Models\NflTeamSchedule;
use App\Models\NFLStadium;

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
        // Train Elo ratings with past seasons
        $this->eloRatingSystem->trainRatingsWithPastSeasons();

        // Get future games that are scheduled
        $futureGames = NflTeamSchedule::whereNull('home_result')
            ->whereNull('away_result')
            ->where('game_status', 'scheduled')
            ->get();

        // Log expected winning percentages and predicted scores
        foreach ($futureGames as $game) {
            $homeStadium = NFLStadium::find($game->team_id_home);
            $awayStadium = NFLStadium::find($game->team_id_away);

            $this->eloRatingSystem->logExpectedWinningPercentageAndPredictedScore($game, $homeStadium, $awayStadium);
        }

        // Get updated Elo ratings
        $ratings = $this->eloRatingSystem->getRatings();

        echo "Updated Elo ratings:\n";
        foreach ($ratings as $team => $rating) {
            echo "Team ID {$team}: {$rating}\n";
        }

        // Calculate expected wins for teams
        $expectedWins = $this->eloRatingSystem->calculateExpectedWinsForTeams();

        echo "\nExpected Wins:\n";
        foreach ($expectedWins as $teamId => $wins) {
            echo "Team ID {$teamId}: " . round($wins, 2) . " expected wins\n";
        }

        // Calculate and log EPA ratings for completed games
        $completedGames = NflTeamSchedule::whereNotNull('home_result')
            ->whereNotNull('away_result')
            ->where('game_status', 'completed')
            ->get();

        echo "\nEPA Ratings:\n";
        foreach ($completedGames as $game) {
            $this->eloRatingSystem->updateEPARatingsAfterGame($game->id);
        }
    }
}
