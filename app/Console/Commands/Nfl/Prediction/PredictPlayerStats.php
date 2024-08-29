<?php

namespace App\Console\Commands\Nfl\Prediction;

use App\Services\Nfl\Calculation\DVOAService;
use App\Services\Nfl\Player\NflPlayerStatsService;
use App\Services\Nfl\Stats\EloRatingService;
use Illuminate\Console\Command;

class PredictPlayerStats extends Command
{
    protected $signature = 'predict:player-stats {player_id} {team_id}';
    protected $description = 'Predict player stats against a given team';

    protected EloRatingService $eloRatingService;
    protected DVOAService $dvoaService;
    protected NflPlayerStatsService $nflPlayerStatsService;

    public function __construct(EloRatingService $eloRatingService, DVOAService $dvoaService, NflPlayerStatsService $nflPlayerStatsService)
    {
        parent::__construct();
        $this->eloRatingService = $eloRatingService;
        $this->dvoaService = $dvoaService;
        $this->nflPlayerStatsService = $nflPlayerStatsService;
    }

    public function handle()
    {
        $playerId = $this->argument('player_id');
        $teamId = $this->argument('team_id');

        $playerTeamId = $this->nflPlayerStatsService->getPlayerTeamId($playerId);
        if (!$playerTeamId) {
            $this->error('Player team ID not found');
            return 1;
        }

        $playerPosition = $this->nflPlayerStatsService->getPlayerPosition($playerId);
        if (!$playerPosition) {
            $this->error('Player position not found');
            return 1;
        }

        $playerTeamElo = $this->eloRatingService->getTeamEloRating($playerTeamId);
        $opposingTeamElo = $this->eloRatingService->getTeamEloRating($teamId);

        if (is_null($playerTeamElo) || is_null($opposingTeamElo)) {
            $this->error('Player team or opposing team Elo rating not found');
            return 1;
        }

        $playerTeamDVOA = $this->dvoaService->calculateTeamDVOA($playerTeamId);
        $opposingTeamDVOA = $this->dvoaService->calculateTeamDVOA($teamId);

        // Predict Rush Yards if available
        $playerAvgRushYards = $this->nflPlayerStatsService->getPlayerAvgRushYards($playerId);
        if (!is_null($playerAvgRushYards)) {
            $teamAvgAllowedRushYards = $this->nflPlayerStatsService->getTeamAvgAllowedRushYards($teamId);
            if (!is_null($teamAvgAllowedRushYards)) {
                $predictedRushYards = $this->eloRatingService->adjustStatsBasedOnElo($playerAvgRushYards, $playerTeamElo, $opposingTeamElo);
                $predictedRushYards += $predictedRushYards * (($playerTeamDVOA - $opposingTeamDVOA) / 100);
                $this->info("Predicted rush yards for player {$playerId} against team {$teamId}: {$predictedRushYards}");
            }
        }

        // Predict Pass Yards if available
        $playerAvgPassYards = $this->nflPlayerStatsService->getPlayerAvgPassYards($playerId);
        if (!is_null($playerAvgPassYards)) {
            $teamAvgAllowedPassYards = $this->nflPlayerStatsService->getTeamAvgAllowedPassYards($teamId);
            if (!is_null($teamAvgAllowedPassYards)) {
                $predictedPassYards = $this->eloRatingService->adjustStatsBasedOnElo($playerAvgPassYards, $playerTeamElo, $opposingTeamElo);
                $predictedPassYards += $predictedPassYards * (($playerTeamDVOA - $opposingTeamDVOA) / 100);
                $this->info("Predicted pass yards for player {$playerId} against team {$teamId}: {$predictedPassYards}");
            }
        }

        // Predict Receiving Yards if available
        $playerAvgRecYards = $this->nflPlayerStatsService->getPlayerAvgRecYards($playerId);
        if (!is_null($playerAvgRecYards)) {
            $teamAvgAllowedRecYards = $this->nflPlayerStatsService->getTeamAvgAllowedRecYards($teamId);
            if (!is_null($teamAvgAllowedRecYards)) {
                $predictedRecYards = $this->eloRatingService->adjustStatsBasedOnElo($playerAvgRecYards, $playerTeamElo, $opposingTeamElo);
                $predictedRecYards += $predictedRecYards * (($playerTeamDVOA - $opposingTeamDVOA) / 100);
                $this->info("Predicted receiving yards for player {$playerId} against team {$teamId}: {$predictedRecYards}");
            }
        }

        return 0;
    }
}
