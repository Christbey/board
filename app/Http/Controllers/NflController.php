<?php

namespace App\Http\Controllers;

use App\Models\NflTeam;
use App\Services\NflPredictionService;
use Illuminate\Http\Request;

class NflController extends Controller
{
    protected NflPredictionService $nflService;

    public function __construct(NflPredictionService $nflService)
    {
        $this->nflService = $nflService;
    }

    public function index()
    {
        $data = $this->nflService->getTeamsWithSchedulesAndOdds();
        return view('nfl.teams', $data);
    }

    public function show($teamId)
    {
        $team = NflTeam::findOrFail($teamId);
        $expectedWins = $this->nflService->eloRatingSystem->calculateExpectedWins($team->id);
        return view('nfl.show', compact('team', 'expectedWins'));
    }
}
