<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;

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

        // Display the box score data
        $this->displayBoxScore($boxScore);
    }

    protected function displayBoxScore(array $boxScore, $prefix = ''): void
    {
        foreach ($boxScore as $key => $value) {
            if (is_array($value)) {
                $this->info($prefix . ucfirst($key) . ':');
                $this->displayBoxScore($value, $prefix . '  ');  // Recursive call for nested arrays
            } else {
                $this->info($prefix . ucfirst($key) . ': ' . $value);
            }
        }
    }
}
