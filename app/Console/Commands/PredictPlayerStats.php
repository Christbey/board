<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EloRatingService;
use App\Services\DVOAService;
use App\Services\NflPlayerStatsService;

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

        $playerAvgRushYards = $this->nflPlayerStatsService->getPlayerAvgRushYards($playerId);
        if (is_null($playerAvgRushYards)) {
            $this->error('Player average rush yards not found');
            return 1;
        }

        $teamAvgAllowedRushYards = $this->nflPlayerStatsService->getTeamAvgAllowedRushYards($teamId);
        if (is_null($teamAvgAllowedRushYards)) {
            $this->error('Team average allowed rush yards not found');
            return 1;
        }

        $playerTeamElo = $this->eloRatingService->getTeamEloRating($playerTeamId);
        if (is_null($playerTeamElo)) {
            $this->error('Player team Elo rating not found');
            return 1;
        }

        $opposingTeamElo = $this->eloRatingService->getTeamEloRating($teamId);
        if (is_null($opposingTeamElo)) {
            $this->error('Opposing team Elo rating not found');
            return 1;
        }

        $playerTeamDVOA = $this->dvoaService->calculateTeamDVOA($playerTeamId);
        $opposingTeamDVOA = $this->dvoaService->calculateTeamDVOA($teamId);

        $predictedRushYards = $this->eloRatingService->adjustStatsBasedOnElo($playerAvgRushYards, $playerTeamElo, $opposingTeamElo);

        $dvoaAdjustmentFactor = ($playerTeamDVOA - $opposingTeamDVOA) / 100;
        $predictedRushYards += $predictedRushYards * $dvoaAdjustmentFactor;

        $this->info("Predicted rush yards for player {$playerId} against team {$teamId}: {$predictedRushYards}");

        return 0;
    }
}
