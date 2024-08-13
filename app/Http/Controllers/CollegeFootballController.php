<?php

namespace App\Http\Controllers;

use App\Models\CollegeFootballAdvGameStat;
use App\Models\CollegeFootballAdvSeasonStat;
use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballEloRating;
use App\Models\CollegeFootballFpiRating;
use App\Models\CollegeFootballRanking;
use App\Models\CollegeFootballTalent;
use App\Models\CollegeFootballTeam;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballPregame;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;

class CollegeFootballController extends Controller
{
    // Function to get the most recent available ratings for a team
    private function getRatings($teamId, $year)
    {
        // Attempt to get FPI and Elo ratings for the specified year
        $fpiRating = CollegeFootballFpiRating::where('team_id', $teamId)
            ->where('year', $year)
            ->first();

        $eloRating = CollegeFootballEloRating::where('team_id', $teamId)
            ->where('year', $year)
            ->first();

        // If neither rating is found, fall back to the most recent available year
        if (!$fpiRating && !$eloRating) {
            $recentFpiRating = CollegeFootballFpiRating::where('team_id', $teamId)
                ->orderBy('year', 'desc')
                ->first();

            $recentEloRating = CollegeFootballEloRating::where('team_id', $teamId)
                ->orderBy('year', 'desc')
                ->first();

            $year = $recentFpiRating->year ?? $recentEloRating->year ?? $year;
            $fpiRating = $recentFpiRating ?? $fpiRating;
            $eloRating = $recentEloRating ?? $eloRating;
        }

        return [$fpiRating, $eloRating, $year];
    }

    public function index(Request $request)
    {
        $conferenceName = $request->input('conference');

        // Fetch the conference based on the selected name
        $conference = CollegeFootballConference::where('name', $conferenceName)->first();

        // Get teams in that conference, or all teams if no conference is selected
        $teams = CollegeFootballTeam::when($conference, function ($query) use ($conference) {
            return $query->where('conference_id', $conference->id);
        })->whereHas('conference', function ($query) {
            $query->where('classification', 'fbs');
        })->get();

        // Fetch Elo ratings for the teams
        $eloRatings = CollegeFootballEloRating::whereIn('team_id', $teams->pluck('id'))
            ->where('year', date('Y')) // Assuming you want the current year's ratings
            ->get()
            ->keyBy('team_id');

        // Fetch all FBS conferences for the filter dropdown
        $conferences = CollegeFootballConference::where('classification', 'fbs')
            ->select('name')
            ->distinct()
            ->get();

        return view('cfb.teams.index', compact('teams', 'conferences', 'conferenceName', 'eloRatings'));
    }

    public function show(Request $request, $team)
    {
        $selectedYear = $request->input('year', date('Y'));

        // Find the team by its id
        $teamData = CollegeFootballTeam::findOrFail($team);

        // Fetch ratings for the team
        [$fpiRating, $eloRating, $year] = $this->getRatings($teamData->id, $selectedYear);

        // Fetch games for this team for the determined year
        $games = CollegeFootballGame::whereYear('start_date', $year)
            ->where(function ($query) use ($teamData) {
                $query->where('home_id', $teamData->id)->orWhere('away_id', $teamData->id);
            })
            ->get();

        // Fetch advanced season stats for the team
        $advStats = CollegeFootballAdvSeasonStat::where('team_id', $teamData->id)
            ->where('season', $year)
            ->first();

        // Fetch talent data for the team
        $talentData = CollegeFootballTalent::where('team_id', $teamData->id)
            ->where('year', $year)
            ->first();

        // Calculate average talent for all teams in the same year
        $averageTalent = CollegeFootballTalent::where('year', $year)->avg('talent');

        // Calculate the conference average FPI
        $averageConferenceFpi = CollegeFootballFpiRating::where('year', $year)
            ->whereHas('team', function ($query) use ($teamData) {
                $query->where('conference_id', $teamData->conference_id);
            })
            ->avg('fpi');

        // Pass the data to the view
        return view('cfb.teams.show', compact('teamData', 'games', 'fpiRating', 'eloRating', 'advStats', 'talentData', 'averageTalent', 'averageConferenceFpi', 'year'));
    }

    public function showEvent($id, $year = null)
    {
        // Find the game or fail
        $game = CollegeFootballGame::findOrFail($id);

        // Determine the year based on the game's start date if not provided
        $year = $year ?? Carbon::parse($game->start_date)->year;

        // Get ratings for home and away teams
        [$homeFpiRating, $homeEloRating, $year] = $this->getRatings($game->home_id, $year);
        [$awayFpiRating, $awayEloRating] = $this->getRatings($game->away_id, $year);

        // Fetch pregame data
        $pregameData = CollegeFootballPregame::where('game_id', $game->id)
            ->where('season', $year)
            ->first();

        $homeAdvStats = CollegeFootballAdvGameStat::where('game_id', $game->id)
            ->where('team_id', $game->home_id)
            ->first();

        $awayAdvStats = CollegeFootballAdvGameStat::where('game_id', $game->id)
            ->where('team_id', $game->away_id)
            ->first();

        // Pass the ratings, game, pregame, and advanced stats data to the view
        return view('cfb.events.show', compact('game', 'homeFpiRating', 'awayFpiRating', 'homeEloRating', 'awayEloRating', 'pregameData', 'homeAdvStats', 'awayAdvStats', 'year'));
    }

    public function event(Request $request)
    {
        $currentYear = date('Y');
        $defaultWeek = 1; // Default to Week 1

        $selectedConference = $request->input('conference', 'SEC'); // Default to SEC
        $selectedWeek = $request->input('week', $defaultWeek); // Default to Week 1
        $selectedYear = $request->input('year', $currentYear); // Default to current year

        Log::info('Selected Conference:', ['conference' => $selectedConference]);
        Log::info('Selected Week:', ['week' => $selectedWeek]);
        Log::info('Selected Year:', ['year' => $selectedYear]);

        // Fetch the conference
        $conference = CollegeFootballConference::where('name', $selectedConference)->first();

        // Fetch games based on the selected conference, week, and year
        $games = CollegeFootballGame::with(['homeTeam', 'awayTeam'])
            ->whereYear('start_date', $selectedYear)
            ->where(function ($query) use ($conference) {
                $query->whereHas('homeTeam', function ($query) use ($conference) {
                    $query->where('conference_id', $conference->id);
                })
                    ->orWhereHas('awayTeam', function ($query) use ($conference) {
                        $query->where('conference_id', $conference->id);
                    });
            })
            ->when($selectedWeek, function ($query) use ($selectedWeek) {
                Log::info('Applying Week Filter:', ['week' => $selectedWeek]);
                return $query->where('week', $selectedWeek);
            })
            ->orderBy('start_date', 'asc')
            ->get();

        Log::info('Fetched Games:', ['games' => $games->toArray()]);

        // Fetch all conferences for the filter dropdown
        $conferences = CollegeFootballConference::pluck('name');

        // Fetch all distinct weeks available in the games table for the selected year
        $weeks = CollegeFootballGame::whereYear('start_date', $selectedYear)
            ->distinct()
            ->orderBy('week')
            ->pluck('week');

        return view('cfb.events.index', compact('games', 'conferences', 'selectedConference', 'weeks', 'selectedWeek'));
    }

    public function rankings(Request $request)
    {
        $selectedPoll = $request->input('poll', 'AP Top 25');
        $currentYear = date('Y'); // Get the current year

        // Fetch distinct polls for the filter dropdown for the current year
        $polls = CollegeFootballRanking::where('season', $currentYear)
            ->select('poll')
            ->distinct()
            ->pluck('poll');

        // Fetch rankings based on the selected poll and current year
        $rankings = CollegeFootballRanking::where('season', $currentYear)
            ->when($selectedPoll, function ($query) use ($selectedPoll) {
                return $query->where('poll', $selectedPoll);
            })
            ->orderBy('points', 'desc')
            ->get();

        return view('cfb.rankings.index', compact('rankings', 'polls', 'selectedPoll'));
    }


}
