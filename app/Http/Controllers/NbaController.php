<?php

namespace App\Http\Controllers;

use App\Models\NbaOdds;
use App\Models\NbaScore;
use App\Models\NbaTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NbaController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function index()
    {
        $teams = NbaTeam::all();
        return view('nba.teams', compact('teams'));
    }

    public function event(Request $request)
    {
        $selectedDate = $request->input('selectedDate', Carbon::today()->format('Y-m-d'));
        $scores = NbaScore::with('homeTeam', 'awayTeam')
            ->whereDate('commence_time', $selectedDate)
            ->get();
        $odds = NbaOdds::whereIn('event_id', $scores->pluck('event_id'))->get();

        return view('nba.event', compact('scores', 'odds', 'selectedDate'));
    }

}
