<?php

namespace App\Http\Controllers;

use App\Services\CollegeFootball\CollegeFootballPredictionService;
use Illuminate\Http\Request;

class CollegeFootballPredictionController extends Controller
{
    protected CollegeFootballPredictionService $predictionService;

    public function __construct(CollegeFootballPredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    public function index(Request $request)
    {
        $weeks = $this->predictionService->getWeeks(2024);
        $games = $request->has('week') && $request->week
            ? $this->predictionService->getGamesByWeek($request->week, 2024)
            : [];

        return view('predict.index', compact('weeks', 'games'));
    }

    public function show($gameId)
    {
        $prediction = $this->predictionService->getPredictionDetails($gameId);

        if (isset($prediction['error'])) {
            return view('predict.game', ['error' => $prediction['error']]);
        }

        return view('predict.game', compact('prediction'));
    }
}
