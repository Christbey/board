<?php

namespace App\Http\Controllers;

use App\Events\UserMadePick;
use App\Models\UserSubmission;

use Illuminate\Http\Request;
use App\Models\NflEspnEvent;
use App\Models\NflEspnWeek;
use Log;
use Carbon\Carbon;

class EspnEventController extends Controller
{
    public function index(Request $request)
    {
        $weekId = $request->input('week_id');
        $weeks = NflEspnWeek::all();

        if ($weekId) {
            $events = NflEspnEvent::where('week_id', $weekId)->get();
        } else {
            $events = NflEspnEvent::all();
        }

        return view('espn.events', compact('events', 'weeks', 'weekId'));
    }

    public function filter(Request $request)
    {
        $weekId = $request->input('week_id');
        return redirect()->route('espn.events', ['week_id' => $weekId]);
    }

    public function showWeekEvents($week_id = null)
    {
        $weeks = NflEspnWeek::where('season_type', 2)
            ->where('season_year', 2024)
            ->get();

        $events = NflEspnEvent::when($week_id, function ($query) use ($week_id) {
            return $query->where('week_id', $week_id);
        })->with(['awayTeam', 'homeTeam'])->get();

        $userSubmissions = UserSubmission::where('user_id', auth()->id())->pluck('team_id', 'event_id')->toArray();

        return view('nfl.picks.week', compact('events', 'weeks', 'week_id', 'userSubmissions'));
    }

    public function pickWinner(Request $request)
    {
        // Validate the request
        $request->validate([
            'event_id' => 'required|exists:nfl_espn_events,id',
            'team_id' => 'required|exists:nfl_espn_teams,team_id',
        ]);

        // Get the event, and the selected team
        $event = NflEspnEvent::findOrFail($request->event_id);
        $selectedTeamId = $request->team_id;

        // Check if the event is completed
        $isCorrect = null;
        if ($event->status_type_completed) {
            // Determine if the user's choice was correct
            $isCorrect = $event->home_team_score > $event->away_team_score
                ? $selectedTeamId == $event->home_team_id
                : $selectedTeamId == $event->away_team_id;
        }

        // Fire the event
        UserMadePick::dispatch(auth()->user(), $event, $selectedTeamId, $isCorrect);

        return redirect()->back()->with('success', 'Your pick has been submitted!')->with('submitted_event_id', $event->id);
    }
}
