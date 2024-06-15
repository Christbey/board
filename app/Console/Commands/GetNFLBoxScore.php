<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NFLStatsService;
use League\Csv\Writer;
use SplTempFileObject;

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

        // Export the box score data to a CSV file
        $this->exportToCsv($boxScore, $gameID);
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

    protected function exportToCsv(array $boxScore, $gameID)
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne(['gameID', 'gameStatus', 'gameDate', 'teamStats']);

        // Extract relevant data
        $data = [
            'gameID' => $gameID,
            'gameStatus' => $boxScore['gameStatus'] ?? 'N/A',
            'gameDate' => $boxScore['teamStats']['gameDate'] ?? 'N/A',
            'teamStats' => json_encode($boxScore['teamStats'] ?? [])
        ];

        $csv->insertOne($data);

        // Save CSV to a file
        $csv->output('nfl_box_score_' . $gameID . '.csv');
    }
}
