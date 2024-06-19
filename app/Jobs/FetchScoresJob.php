<?php

// app/Jobs/FetchScoresJob.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Traits\FetchScoresTrait;
use Illuminate\Support\Facades\Log;

class FetchScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FetchScoresTrait;

    protected $sport;

    public function __construct($sport)
    {
        $this->sport = $sport;
    }

    public function handle(): void
    {
        $sport = $this->sport;

        Log::info("Starting FetchScoresJob for {$sport}");

        $endpoint = $this->getEndpoint($sport);
        $modelClass = $this->getModelClass($sport);
        $teamModelClass = $this->getTeamModelClass($sport);

        if ($endpoint && $modelClass && $teamModelClass) {
            $this->fetchAndStoreScores($sport, $endpoint, $modelClass, $teamModelClass);
            Log::info("Completed FetchScoresJob for {$sport}");
        } else {
            Log::error('Invalid sport provided.');
        }
    }

    protected function getEndpoint($sport): ?string
    {
        $endpoints = [
            'mlb' => 'sports/baseball_mlb/scores',
            'nba' => 'sports/basketball_nba/scores',
            'nfl' => 'sports/americanfootball_nfl/scores',
            'ncaa' => 'sports/americanfootball_ncaaf/scores',
        ];

        return $endpoints[$sport] ?? null;
    }

    protected function getModelClass($sport)
    {
        $models = [
            'mlb' => \App\Models\MlbScore::class,
            'nba' => \App\Models\NbaScore::class,
            'nfl' => \App\Models\NflScore::class,
            'ncaa' => \App\Models\NcaaScore::class,
        ];

        return $models[$sport] ?? null;
    }

    protected function getTeamModelClass($sport): ?string
    {
        $models = [
            'mlb' => \App\Models\MlbTeam::class,
            'nba' => \App\Models\NbaTeam::class,
            'nfl' => \App\Models\NflTeam::class,
            'ncaa' => \App\Models\NcaaTeam::class,
        ];

        return $models[$sport] ?? null;
    }
}
