<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\EloRatingService;
use App\Services\DVOAService;

class PredictPlayerStats extends Command
{
    protected $signature = 'predict:player-stats {player_id} {team_id}';
    protected $description = 'Predict player stats against a given team';
    protected $eloRatingService;
    protected $dvoaService;

    public function __construct(EloRatingService $eloRatingService, DVOAService $dvoaService)
    {
        parent::__construct();
        $this->eloRatingService = $eloRatingService;
        $this->dvoaService = $dvoaService;
    }

    public function handle()
    {
        $playerId = $this->argument('player_id');
        $teamId = $this->argument('team_id');

        // Get player's team ID
        $playerTeamId = DB::table('nfl_player_stats')
            ->where('player_id', $playerId)
            ->value('team_id');

        if (!$playerTeamId) {
            $this->error('Player team ID not found');
            return 1;
        }

        // Get player's position using player_id
        $playerPosition = DB::table('nfl_players')
            ->where('player_id', $playerId)
            ->value('pos');

        if (!$playerPosition) {
            $this->error('Player position not found');
            return 1;
        }

        // Get player's average rush yards
        $playerAvgRushYards = DB::table('nfl_player_stats')
            ->where('player_id', $playerId)
            ->avg('rush_yards');

        if (is_null($playerAvgRushYards)) {
            $this->error('Player average rush yards not found');
            return 1;
        }

        // Get average rush yards allowed by the provided team
        $teamAvgAllowedRushYards = DB::table('nfl_player_stats')
            ->join('nfl_team_schedules', function ($join) use ($teamId) {
                $join->on('nfl_player_stats.team_id', '=', 'nfl_team_schedules.team_id_home')
                    ->orOn('nfl_player_stats.team_id', '=', 'nfl_team_schedules.team_id_away');
            })
            ->where(function ($query) use ($teamId) {
                $query->where('team_id_home', $teamId)
                    ->orWhere('team_id_away', $teamId);
            })
            ->avg('rush_yards');

        if (is_null($teamAvgAllowedRushYards)) {
            $this->error('Team average allowed rush yards not found');
            return 1;
        }

        // Get Elo ratings
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

        // Calculate DVOA for both teams
        $playerTeamDVOA = $this->dvoaService->calculateTeamDVOA($playerTeamId);
        $opposingTeamDVOA = $this->dvoaService->calculateTeamDVOA($teamId);

        // Adjust player's average stats based on Elo difference and DVOA
        $predictedRushYards = $this->eloRatingService->adjustStatsBasedOnElo($playerAvgRushYards, $playerTeamElo, $opposingTeamElo);

        // Incorporate DVOA into prediction (simple linear adjustment for demonstration)
        $dvoaAdjustmentFactor = ($playerTeamDVOA - $opposingTeamDVOA) / 100;
        $predictedRushYards += $predictedRushYards * $dvoaAdjustmentFactor;

        // Output the prediction
        $this->info("Predicted rush yards for player {$playerId} against team {$teamId}: {$predictedRushYards}");

        return 0;
    }
}
