<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballPregame;
use LaravelIdea\Helper\App\Models\_IH_CollegeFootballGame_C;
use Log;

class GameCollegeFootball extends Command
{
    protected $signature = 'compare:games {year}';
    protected $description = 'Compare NFL and college football games for a given year';

    public function handle()
    {
        $year = $this->argument('year');

        // Fetch games and pregame data
        $collegeFootballGames = $this->collegeFootballGame($year);
        $collegeFootballPregames = $this->collegeFootballPregame($year);

        // Example of merging or comparing data
        foreach ($collegeFootballGames as $game) {
            $pregame = $collegeFootballPregames->firstWhere('game_id', $game->id);

            if ($pregame) {
                // Perform comparison or any other operation here
                $this->info("Game ID: {$game->id} has pregame data.");
                // Add more logic to compare or manipulate the data as needed
            } else {
                $this->warn("Game ID: {$game->id} does not have pregame data.");
            }

            Log::Info('has pregame data');
        }
    }

    public function collegeFootballGame($year)
    {
        return CollegeFootballGame::where('season', $year)
            ->where('season_type', 'regular')
            ->where('week', '1')
            ->where('home_division', 'fbs')
            ->where('completed', 1)
            ->get();
    }

    public function collegeFootballPregame($year)
    {
        return CollegeFootballPregame::where('season', $year)
            ->where('season_type', 'regular')
            ->where('week', '1')
            ->get();
    }


    public function collegeFootballPregameFiltered(): _IH_CollegeFootballGame_C|array
    {
        return CollegeFootballGame::join(
            'college_football_pregame',
            'college_football_games.id',
            '=',
            'college_football_pregame.game_id'
        )
            ->join('college_football_fpi_ratings as home_fpi', function ($join) {
                $join
                    ->on('college_football_games.home_team_id', '=', 'home_fpi.team_id')
                    ->where('home_fpi.year', '=', 2024);
            })
            ->join('college_football_fpi_ratings as away_fpi', function ($join) {
                $join
                    ->on('college_football_games.away_team_id', '=', 'away_fpi.team_id')
                    ->where('away_fpi.year', '=', 2024);
            })
            ->where('college_football_games.season', 2024)
            ->where('college_football_games.season_type', 'regular')
            ->where('college_football_games.week', 1)
            ->where('college_football_games.home_division', 'fbs')
            ->where('college_football_games.completed', 0)
            ->select(
                'college_football_games.id',
                'college_football_games.season',
                'college_football_games.week',
                'college_football_games.start_date',
                'college_football_games.venue',
                'college_football_games.home_team',
                'college_football_games.away_team',
                'college_football_games.home_pregame_elo',
                'college_football_games.away_pregame_elo',
                'college_football_pregame.spread', // Adjusted to fetch from the correct table
                'college_football_pregame.home_win_prob', // Adjusted to fetch from the correct table
                DB::raw('MAX(home_fpi.fpi) as home_fpi_rating'),
                DB::raw('MAX(away_fpi.fpi) as away_fpi_rating')
            )
            ->groupBy('college_football_games.id') // Group by game ID
            ->get();

    }
}
