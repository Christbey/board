<?php

namespace App\Console\Commands\Nfl;

use Illuminate\Console\Command;
use App\Models\NflTeamSchedule;
use App\Jobs\FetchNFLBoxScore;

class GetNFLBoxScore extends Command
{
    protected $signature = 'nfl:fetch-boxscore {game_id?}';
    protected $description = 'Fetch NFL box score and store in database';

    public function handle(): void
    {
        $gameID = $this->argument('game_id');

        if ($gameID) {
            $this->info("Dispatching job to fetch box score for game: {$gameID}");
            FetchNFLBoxScore::dispatch($gameID);
        } else {
            $gameIDs = NflTeamSchedule::pluck('game_id');

            foreach ($gameIDs as $gameID) {
                FetchNFLBoxScore::dispatch($gameID);
            }
        }
    }
}
