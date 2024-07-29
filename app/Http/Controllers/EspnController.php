<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EspnController extends Controller
{
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
                        $predictorResponse = Http::timeout(60)->retry(3, 100)->get($predictorUrl);
                        if ($predictorResponse->successful()) {
                            $event['predictor'] = $predictorResponse->json();
                        } else {
                            Log::error("Failed to fetch predictor data from $predictorUrl");
                        }
                    } catch (ConnectionException $e) {
                        Log::error('Connection error: ' . $e->getMessage());
                    }
                }
            }
        }

        // Pass the output to the view
        return view('espn.team-details', ['teamData' => $teamData]);
    }

    public function showDepthChart()
    {
        $output = new BufferedOutput();

        // Call the command
        Artisan::call('espn:nfl-player-depth-chart', [], $output);

        // Get the output
        $commandOutput = $output->fetch();

        // Decode the JSON output
        $depthChartData = json_decode($commandOutput, true);

        // Log the data for debugging
        if ($depthChartData !== null) {
            Log::info('Depth Chart Data:', $depthChartData);
        } else {
            Log::error('Failed to decode Depth Chart Data:', ['output' => $commandOutput]);
        }

        // Pass the output to the view
        return view('espn.depth-chart', ['depthChartData' => $depthChartData]);
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

}
