<?php
// THIS FILE WORKS, IT STORES PLAYER STATS
namespace App\Console\Commands\Nfl\Stat;

use App\Jobs\FetchNFLBoxScore;
use App\Models\NflTeamSchedule;
use Illuminate\Console\Command;

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
