<?php

namespace App\Http\Controllers;

use App\Models\NflTeam;
use App\Services\NflPredictionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NflController extends Controller
{
    protected NflPredictionService $nflPredictionService;

    public function __construct(NflPredictionService $nflPredictionService)
    {
        $this->nflPredictionService = $nflPredictionService;
    }

    public function index()
    {
        try {
            $data = $this->nflPredictionService->getTeamsWithSchedulesAndOdds();
            Log::info('Expected Wins Data:', $data['expectedWins']);
            return view('nfl.teams', $data);
        } catch (Exception $e) {
            Log::error('Error fetching teams with schedules and odds: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->view('errors.500', [], 500);
        }
    }

    public function show($teamId)
    {
        try {
            $team = NflTeam::findOrFail($teamId);
            $expectedWins = $this->nflPredictionService->eloRatingSystem->calculateExpectedWins($team->id);
            return view('nfl.show', compact('team', 'expectedWins'));
        } catch (Exception $e) {
            Log::error('Error fetching team details: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return response()->view('errors.500', [], 500);
        }
    }
}
