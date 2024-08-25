<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\CollegeFootballGame;
use App\Models\NcaaOdds;
use Illuminate\Support\Facades\Log;

class LinkNcaaOdds extends Command
{
    protected $signature = 'link:ncaa-odds';
    protected $description = 'Link NCAA Odds with College Football Games';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Fetch all college football games for the 2024 season
        $games = CollegeFootballGame::where('season', 2024)->get();

        foreach ($games as $game) {
            // Check if start_date is null
            if (!$game->start_date) {
                $this->warn("Game ID {$game->id} does not have a start_date.");
                continue;
            }

            // Fetch matching odds based on NCAA team IDs and date
            $odds = NcaaOdds::whereHas('homeTeam', function ($query) use ($game) {
                $query->where('cf_team_id', $game->home_team_id);
            })
                ->whereHas('awayTeam', function ($query) use ($game) {
                    $query->where('cf_team_id', $game->away_team_id);
                })
                ->where('commence_time', '=', $game->start_date)
                ->first();

            // Check if odds were found before trying to access properties
            if ($odds) {
                // Update the game with the found odds ID
                $game->ncaa_odds_id = $odds->id;
                $game->save();

                $this->info("Linked game ID {$game->id} with odds ID {$odds->id}.");
            } else {
                $this->warn("No matching odds found for game ID {$game->id} on date {$game->start_date}.");
            }
        }

        $this->info('Linking process completed.');
        return 0;
    }
}
