<?php

namespace App\Http\Controllers;

use App\Services\OddsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OddsController extends Controller
{
    protected $oddsApiService;

    public function __construct(OddsApiService $oddsApiService)
    {
        $this->oddsApiService = $oddsApiService;
    }

    public function index()
    {
        $sports = $this->oddsApiService->getSports();
        return view('odds.index', compact('sports'));
    }

    public function fetch(Request $request)
    {
        $sport = $request->input('sport');
        $markets = 'h2h,spreads,totals';

        $odds = $this->oddsApiService->getOdds($sport, $markets);
        Log::info("Odds API Response for {$sport}: " . json_encode($odds));

        return view('odds.show', compact('odds', 'sport'));
    }
}
