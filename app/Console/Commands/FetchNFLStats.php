<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;

class FetchNFLStats extends Command
{
    protected $signature = 'fetch:nfl-stats {playerId}';
    protected $description = 'Fetch NFL stats for a player';
    protected $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle()
    {
        $playerId = $this->argument('playerId');
        $stats = $this->nflStatsService->getPlayerStats($playerId);

        if (empty($stats)) {
            $this->error('No games found for the given player.');
            return;
        }

        foreach ($stats as $gameId => $game) {
            $this->info('Game ID: ' . $gameId);
            $this->info('Team: ' . $game['teamAbv']);

            if (isset($game['longName'])) {
                $this->info('Player: ' . $game['longName']);
            } else {
                $this->info('Player: N/A');
            }

            if (isset($game['Passing'])) {
                $this->info('Passing Yards: ' . $game['Passing']['passYds']);
                $this->info('Touchdowns: ' . $game['Passing']['passTD']);
            }

            if (isset($game['Rushing'])) {
                $this->info('Rushing Yards: ' . $game['Rushing']['rushYds']);
                $this->info('Rushing Touchdowns: ' . $game['Rushing']['rushTD']);
            }

            if (isset($game['Defense'])) {
                $this->info('Fumbles: ' . $game['Defense']['fumbles']);
                $this->info('Fumbles Lost: ' . $game['Defense']['fumblesLost']);
            }

            if (isset($game['fantasyPoints'])) {
                $this->info('Fantasy Points: ' . $game['fantasyPoints']);
            }

            $this->info('---');
        }
    }
}
