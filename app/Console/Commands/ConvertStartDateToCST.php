<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballGame;
use Carbon\Carbon;

class ConvertStartDateToCST extends Command
{
    protected $signature = 'convert:start-date-cst';
    protected $description = 'Convert College Football Game start dates to CST';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Fetch all college football games
        $games = CollegeFootballGame::all();

        foreach ($games as $game) {
            // Check if start_date is not null
            if ($game->start_date) {
                // Convert start_date to CST
                $cstDate = Carbon::parse($game->start_date)->setTimezone('America/Chicago');

                // Update the game record with the CST start date
                $game->start_date = $cstDate->format('Y-m-d H:i:s');
                $game->save();

                $this->info("Updated game ID {$game->id} start date to CST: {$cstDate->format('Y-m-d H:i:s')}");
            } else {
                $this->warn("Game ID {$game->id} does not have a start_date.");
            }
        }

        $this->info('Start date conversion to CST completed.');
        return 0;
    }
}
