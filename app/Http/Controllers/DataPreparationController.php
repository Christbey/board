<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataPreparationService;

class DataPreparationController extends Controller
{
    protected $dataPreparationService;

    public function __construct(DataPreparationService $dataPreparationService)
    {
        $this->dataPreparationService = $dataPreparationService;
    }

    public function fetchData(Request $request)
    {
        $result = $this->dataPreparationService->fetchData($request);

        if (isset($result['message'])) {
            return view('data_preparation', [
                'message' => $result['message'],
                'teamNames' => $this->dataPreparationService->getTeamNames()
            ]);
        }

        return view('data_preparation', [
            'predictions' => $result['predictions'] ?? [],
            'winCountsWithNames' => $result['winCountsWithNames'] ?? [],
            'strengthOfSchedule' => $result['strengthOfSchedule'] ?? [],
            'homeTeamsCoverSpreadCount' => $result['homeTeamsCoverSpreadCount'] ?? 0,
            'teamNames' => $this->dataPreparationService->getTeamNames()
        ]);
    }
}
