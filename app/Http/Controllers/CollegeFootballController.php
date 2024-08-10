<?php

namespace App\Http\Controllers;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballEloRating;
use App\Models\CollegeFootballFpiRating;
use Illuminate\Http\Request;
use App\Models\CollegeFootballTeam;
use App\Models\CollegeFootballGame;
use Log;

class CollegeFootballController extends Controller
{
    /**
     * Display a listing of college football teams or data.
     */
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

        return view('college-football.index', compact('teams', 'conferences', 'conferenceName', 'eloRatings'));
    }


    public function show($team)
    {
        // Find the team by its id (assuming it's a unique identifier)
        $teamData = CollegeFootballTeam::where('id', $team)->firstOrFail();

        // Fetch the FPI rating for this team
        $fpiRating = CollegeFootballFpiRating::where('team_id', $teamData->id)
            ->where('year', date('Y')) // Assuming you want the current year's rating
            ->first();

        // Fetch the Elo rating for this team
        $eloRating = CollegeFootballEloRating::where('team_id', $teamData->id)
            ->where('year', date('Y')) // Assuming you want the current year's rating
            ->first();

        // Fetch games where this team is either the home or away team
        $games = CollegeFootballGame::where(function ($query) use ($teamData) {
            $query->where('home_id', $teamData->id)
                ->orWhere('away_id', $teamData->id);
        })->get();

        // Pass the data to the view
        return view('college-football.show', compact('teamData', 'games', 'fpiRating', 'eloRating'));
    }
    
}
