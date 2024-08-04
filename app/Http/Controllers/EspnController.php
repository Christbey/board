<?php

namespace App\Http\Controllers;

use App\Models\EspnNflDepthChart;
use App\Models\EspnNflPastH2h;
use App\Models\NflEspnAtsRecord;
use App\Models\NflEspnEvent;
use App\Models\NflEspnEventOdd;
use App\Models\NflEspnFuture;
use App\Models\NflEspnInjury;
use App\Models\NflEspnTeam;
use App\Models\NflEspnTeamProjection;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Http\Client\ConnectionException;

class EspnController extends Controller
{

    public function index()
    {
        $teams = NflEspnTeam::all();
        return view('espn.nfl.teams.index', compact('teams'));
    }

    public function showTeamDetails($team_id, $year = 2023)
    {
        $output = new BufferedOutput();

        // Call the command with the team_id and year
        Artisan::call('espn:team-details', ['team_id' => $team_id, 'year' => $year], $output);

        // Get the output
        $commandOutput = $output->fetch();

        // Decode the JSON output
        $teamData = json_decode($commandOutput, true);

        // Fetch predictor data for each event
        if (isset($teamData['events']['items'])) {
            foreach ($teamData['events']['items'] as &$event) {
                if (isset($event['competitions'][0]['predictor']['$ref'])) {
                    $predictorUrl = htmlspecialchars_decode($event['competitions'][0]['predictor']['$ref'], ENT_QUOTES);
                    try {
                        $predictorResponse = Http::timeout(120)->retry(5, 200)->get($predictorUrl);
                        if ($predictorResponse->successful()) {
                            $event['predictor'] = $predictorResponse->json();
                        } else {
                            Log::error("Failed to fetch predictor data from $predictorUrl", ['response' => $predictorResponse->body()]);
                        }
                    } catch (ConnectionException $e) {
                        Log::error('Connection error: ' . $e->getMessage(), ['url' => $predictorUrl]);
                    } catch (Exception $e) {
                        Log::error('Unexpected error: ' . $e->getMessage(), ['url' => $predictorUrl]);
                    }
                }
            }
        }

        // Get the list of teams
        $teams = NflEspnTeam::all();

        // Pass the output to the view
        return view('espn.team-details', ['teamData' => $teamData, 'teams' => $teams, 'selectedTeamId' => $team_id]);
    }

    public function showDepthChart(Request $request)
    {
        $teamId = $request->input('team_id');

        if ($teamId) {
            $output = new BufferedOutput();

            // Call the command with the team_id if no depth chart data exists for the team
            $depthChartData = EspnNflDepthChart::where('team_id', $teamId)->get();
            if ($depthChartData->isEmpty()) {
                Artisan::call('espn:nfl-player-depth-chart', ['team_id' => $teamId], $output);
                $depthChartData = EspnNflDepthChart::where('team_id', $teamId)->get();
            }
        } else {
            $depthChartData = collect();
        }

        // Fetch all teams for the dropdown
        $teams = NflEspnTeam::all();

        // Log the data for debugging
        if ($depthChartData->isNotEmpty()) {
            Log::info('Depth Chart Data:', $depthChartData->toArray());
        } else {
            Log::error('No Depth Chart Data found for team_id:', ['team_id' => $teamId]);
        }

        // Pass the output to the view
        return view('espn.depth-chart', [
            'depthChartData' => $depthChartData,
            'teams' => $teams,
        ]);
    }

    public function showNflSchedule($team_id)
    {
        $output = new BufferedOutput();

        // Call the command with the team_id
        Artisan::call('espn:nfl-schedule', ['team_id' => $team_id], $output);

        // Get the output
        $commandOutput = $output->fetch();

        // Decode the JSON output
        $scheduleData = json_decode($commandOutput, true);

        // Log the data for debugging
        if ($scheduleData !== null) {
            Log::info('Schedule Data:', $scheduleData);
        } else {
            Log::error('Failed to decode Schedule Data:', ['output' => $commandOutput]);
        }

        // Pass the output to the view
        return view('espn.schedule', ['scheduleData' => $scheduleData]);
    }

    public function showNflOdds()
    {
        // Read the stored JSON data
        $oddsData = json_decode(Storage::get('public/nfl_odds.json'), true);

        // Log the data for debugging
        if ($oddsData !== null) {
            Log::info('Odds Data:', $oddsData);
        } else {
            Log::error('Failed to read Odds Data from storage');
        }

        return view('espn.nfl-odds', ['oddsData' => $oddsData]);
    }

    public function showNflScoreboard()
    {
        // Read the stored JSON data
        $scoreboardData = json_decode(Storage::get('public/nfl_scoreboard.json'), true);

        // Log the data for debugging
        if ($scoreboardData !== null) {
            Log::info('Scoreboard Data:', $scoreboardData);
        } else {
            Log::error('Failed to read Scoreboard Data from storage');
        }

        return view('espn.nfl-scoreboard', ['scoreboardData' => $scoreboardData]);
    }

    public function showNflTeamProjection()
    {
        // Read the stored JSON data
        $projectionData = json_decode(Storage::get('public/nfl_team_projection.json'), true);

        // Log the data for debugging
        if ($projectionData !== null) {
            Log::info('Projection Data:', $projectionData);
        } else {
            Log::error('Failed to read Projection Data from storage');
        }

        return view('espn.nfl_team_projection', ['projectionData' => $projectionData]);
    }

    public function filterTeam(Request $request)
    {
        $teamId = $request->input('team_id');

        return redirect()->route('espn.team-details', ['team_id' => $teamId]);
    }

    public function showInjuries(Request $request)
    {
        $teamId = $request->input('team_id');
        $teams = NflEspnTeam::all();

        if ($teamId) {
            $injuries = NflEspnInjury::with(['team', 'athlete'])->where('team_id', $teamId)->get();
        } else {
            $injuries = NflEspnInjury::with(['team', 'athlete'])->get();
        }

        return view('espn.injuries', compact('injuries', 'teams', 'teamId'));
    }

    public function show($id)
    {
        $team = NflEspnTeam::findOrFail($id);
        $injuries = NflEspnInjury::with(['team', 'athlete'])->where('team_id', $id)->get();
        $atsRecords = NflEspnAtsRecord::where('team_id', $id)->get();
        $events = NflEspnEvent::where('home_team_id', $id)
            ->orWhere('away_team_id', $id)
            ->get();
        $futures = NflEspnFuture::where('team_id', $id)->get();
        $depthChart = EspnNflDepthChart::where('team_id', $id)->get();
        $projections = NflEspnTeamProjection::where('team_id', $id)->get();

        return view('espn.nfl.teams.show', compact('team', 'injuries', 'atsRecords', 'events', 'futures', 'depthChart', 'projections'));
    }

    public function showEvent($id)
    {
        $event = NflEspnEvent::findOrFail($id);
        $odds = NflEspnEventOdd::where('event_id', $event->event_id)
            ->get(['provider_name', 'over_under', 'spread', 'over_odds', 'under_odds']);

        $homeTeamId = $event->home_team_id;
        $awayTeamId = $event->away_team_id;

        $pastH2h = EspnNflPastH2h::where(function ($query) use ($homeTeamId, $awayTeamId) {
            $query->where('home_team_id', $homeTeamId)
                ->where('away_team_id', $awayTeamId);
        })->orWhere(function ($query) use ($homeTeamId, $awayTeamId) {
            $query->where('home_team_id', $awayTeamId)
                ->where('away_team_id', $homeTeamId);
        })->get();

        return view('espn.nfl.events.show', compact('event', 'odds', 'pastH2h'));
    }


}