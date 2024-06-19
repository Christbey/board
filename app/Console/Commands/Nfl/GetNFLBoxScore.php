<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use App\Jobs\NflPlayerStatsJob;

class GetNFLBoxScore extends Command
{
    protected $signature = 'nfl:get-boxscore {gameID} {--playByPlay}';
    protected $description = 'Get NFL Game Box Score - Live Real Time';
    protected NFLStatsService $nflStatsService;

    public function __construct(NFLStatsService $nflStatsService)
    {
        parent::__construct();
        $this->nflStatsService = $nflStatsService;
    }

    public function handle(): void
    {
        $gameID = $this->argument('gameID');
        $playByPlay = $this->option('playByPlay');

        $this->info('Fetching box score for game: ' . $gameID);

        $response = $this->nflStatsService->getNFLBoxScore($gameID, $playByPlay);
        $boxScore = $response['body'] ?? [];

        if (empty($boxScore)) {
            $this->error('No box score data found.');
            return;
        }

        // Dispatch job with player stats
        NflPlayerStatsJob::dispatch($gameID, $boxScore['playerStats'] ?? []);
    }
}
