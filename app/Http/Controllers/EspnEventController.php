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

        // Get the event and the selected team
        $event = NflEspnEvent::findOrFail($request->event_id);
        $selectedTeamId = $request->team_id;

        // Get the week number and the dates from the config
        $weekNumber = $event->week->week_number; // Using week->week_number from the event model
        $weekDates = config('nfl.weeks')[$weekNumber];

        // Get the end date of the week for the event
        $weekEndDate = Carbon::parse($weekDates['end']);
        $currentDate = Carbon::now();

        // Check if the current date is after the week's end date
        if ($currentDate->isAfter($weekEndDate)) {
            return redirect()->back()->with('error', 'Submissions for this week are locked.')
                ->with('submitted_event_id', $event->id);
        }

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

// Controller method
    public function showSubmissions(Request $request, $weekId = null)
    {
        $user = auth()->user();
        $weekId = $request->input('week_id', $weekId);

        // Fetch the weeks with season_type = 2 and events where the date is LIKE '2024%'
        $weeks = NflEspnWeek::where('season_type', 2)
            ->whereHas('events', function ($query) {
                $query->where('date', 'LIKE', '2024%');
            })->get();

        // Fetch the week details for the selected week, ensuring it matches the date condition and season_type
        $week = NflEspnWeek::where('id', $weekId)
            ->where('season_type', 2)
            ->whereHas('events', function ($query) {
                $query->where('date', 'LIKE', '2024%');
            })
            ->firstOrFail();
        $weekNumber = $week->week_number;


        // Get the user's submissions for the specified week
        $userSubmissions = UserSubmission::with(['event', 'team'])
            ->where('user_id', $user->id)
            ->whereHas('event', function ($query) use ($weekId) {
                $query->where('week_id', $weekId);
            })
            ->get();

        // Calculate correct picks for the week and season, and ranks
        $correctPicksThisWeek = $this->calculateCorrectPicksThisWeek($userSubmissions);
        $correctPicksTotal = $this->calculateCorrectPicksTotal($user->id);
        $rankThisWeek = $this->calculateRank($user->id, $weekId);
        $rankTotal = $this->calculateRank($user->id);
        $teamSelectionPercentages = $this->calculateTeamSelectionPercentages($weekId);

        return view('picks.submissions', compact(
            'userSubmissions',
            'correctPicksThisWeek',
            'correctPicksTotal',
            'rankThisWeek',
            'rankTotal',
            'teamSelectionPercentages',
            'weekNumber',
            'weekId',
            'weeks'
        ));
    }

    protected function calculateCorrectPicksThisWeek($userSubmissions)
    {
        return $userSubmissions->map(function ($submission) {
            $event = $submission->event;
            if ($event->status_type_completed) {
                if ($event->home_team_score > $event->away_team_score) {
                    $submission->is_correct = $submission->team_id == $event->home_team_id;
                } elseif ($event->away_team_score > $event->home_team_score) {
                    $submission->is_correct = $submission->team_id == $event->away_team_id;
                } else {
                    $submission->is_correct = false;
                }
                // Save the updated `is_correct` status
                $submission->save();
            }
            return $submission->is_correct;
        })->filter()->count(); // Return the count of correct picks
    }

    protected function calculateCorrectPicksTotal($userId)
    {
        $submissions = UserSubmission::where('user_id', $userId)
            ->whereHas('event', function ($query) {
                $query->where('status_type_completed', true);
            })
            ->get();

        return $submissions->map(function ($submission) {
            $event = $submission->event;
            if ($event->home_team_score > $event->away_team_score) {
                $submission->is_correct = $submission->team_id == $event->home_team_id;
            } elseif ($event->away_team_score > $event->home_team_score) {
                $submission->is_correct = $submission->team_id == $event->away_team_id;
            } else {
                $submission->is_correct = false;
            }
            // Save the updated `is_correct` status
            $submission->save();

            return $submission->is_correct;
        })->filter()->count(); // Return the count of correct picks
    }

    protected function calculateRank($userId, $weekId = null)
    {
        $correctPicks = $weekId
            ? $this->calculateCorrectPicksThisWeek(UserSubmission::where('user_id', $userId)
                ->whereHas('event', function ($query) use ($weekId) {
                    $query->where('week_id', $weekId);
                })->get())
            : $this->calculateCorrectPicksTotal($userId);

        $rankings = UserSubmission::whereHas('event', function ($query) use ($weekId) {
            if ($weekId) {
                $query->where('week_id', $weekId);
            }
        })
            ->get()
            ->groupBy('user_id')
            ->sortByDesc(function ($submissions) {
                return $submissions->count();
            })
            ->keys()
            ->toArray();

        // Find the position of the current user in the rankings array
        $rank = array_search($userId, $rankings);

        // If the user is not found in the rankings, return a default rank (e.g., last place)
        return $rank !== false ? $rank + 1 : count($rankings) + 1;
    }

    protected function calculateTeamSelectionPercentages($weekId)
    {
        $submissions = UserSubmission::whereHas('event', function ($query) use ($weekId) {
            $query->where('week_id', $weekId);
        })->get();

        $submissionsGrouped = $submissions->groupBy(['event_id', 'team_id']);

        $percentages = [];

        foreach ($submissionsGrouped as $eventId => $teams) {
            $totalPicksForEvent = $teams->sum(function ($teamPicks) {
                return $teamPicks->count();
            });

            foreach ($teams as $teamId => $teamPicks) {
                $percentages[$eventId][$teamId] = ($teamPicks->count() / $totalPicksForEvent) * 100;
            }
        }

        return $percentages;
    }

}
