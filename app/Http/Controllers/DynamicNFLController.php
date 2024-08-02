<?php

namespace App\Http\Controllers;

use App\Services\ESPNService;
use Illuminate\Http\Request;

class DynamicNFLController extends Controller
{
    protected $espnService;

    public function __construct(ESPNService $espnService)
    {
        $this->espnService = $espnService;
    }

    public function fetch(Request $request)
    {
        $endpoint = $request->input('endpoint');
        $base = $request->input('base', 'default');
        $params = $request->query(); // Get all query parameters
        $data = $this->espnService->getData($endpoint, $params, $base);
        return view('nfl.dynamic', compact('data'));
    }

    public function fetchAndSaveTeams(Request $request)
    {
        $params = $request->query(); // Get all query parameters
        $base = $request->input('base', 'core');
        $this->espnService->saveTeamsData($params, $base);
        return response()->json(['message' => 'Teams data fetched and stored successfully']);
    }
}

