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

    public function fetchData()
    {
        $result = $this->dataPreparationService->fetchData();

        if (isset($result['message'])) {
            return view('data_preparation', ['message' => $result['message']]);
        }

        return view('data_preparation', [
            'predictions' => $result['predictions'],
            'winCountsWithNames' => $result['winCountsWithNames']
        ]);
    }
}
