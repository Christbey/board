<?php

namespace App\Http\Controllers;


use App\Models\MlbOdds;
use App\Models\MlbScore;
use App\Models\MlbTeam;
use App\Services\MlbOddsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MlbController extends Controller
{
    protected $mlbOddsService;
    protected $apiKey;
    protected $baseUrl;

    public function __construct(MlbOddsService $mlbOddsService)
    {
        $this->mlbOddsService = $mlbOddsService;
        $this->apiKey = config('services.oddsapi.key');
        $this->baseUrl = config('services.oddsapi.base_url');
    }

    public function showOdds()
    {
        $today = Carbon::today();
        $odds = MlbOdds::whereDate('commence_time', $today)->get();

        return view('mlb.odds', [
            'odds' => $odds,
            'sport' => 'MLB' // Or fetch dynamically as needed
        ]);
    }

    public function index()
    {
        // Fetch all MLB teams
        $teams = MlbTeam::all();

        // Return the view with the teams data
        return view('mlb.teams', compact('teams'));
    }

    public function showScores()
    {
        $this->fetchScores(); // Fetch scores from the API and store them

        $today = Carbon::now('America/Chicago')->format('Y-m-d');
        $scores = MlbScore::with(['homeTeam', 'awayTeam'])
            ->whereDate('commence_time', $today)
            ->get()
            ->sortBy(function($score) {
                if ($score->completed) {
                    return PHP_INT_MAX; // Completed events are sorted last
                } elseif ($score->home_team_score !== null && $score->away_team_score !== null) {
                    return Carbon::parse($score->commence_time)->timestamp; // Live events are sorted by start time
                } else {
                    return PHP_INT_MAX - 1; // Upcoming events
                }
            });

        return view('mlb.scores', compact('scores'));
    }

    protected function fetchScores()
    {
        $response = Http::get("{$this->baseUrl}/sports/baseball_mlb/scores", [
            'apiKey' => $this->apiKey,
            'daysFrom' => 3,
            'dateFormat' => 'iso',
        ]);

        if ($response->successful()) {
            $this->storeScores($response->json());
        } else {
            Log::error('Failed to fetch MLB scores from the API.');
        }
    }

    protected function storeScores(array $scoresData)
    {
        foreach ($scoresData as $score) {
            $homeTeam = MlbTeam::where('name', $score['home_team'])->first();
            $awayTeam = MlbTeam::where('name', $score['away_team'])->first();

            if ($homeTeam && $awayTeam) {
                MlbScore::updateOrCreate(
                    ['event_id' => $score['id']],
                    [
                        'sport_key' => $score['sport_key'],
                        'sport_title' => $score['sport_title'],
                        'commence_time' => Carbon::parse($score['commence_time'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s'),
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $awayTeam->id,
                        'home_team_score' => $score['scores'][0]['score'] ?? null,
                        'away_team_score' => $score['scores'][1]['score'] ?? null,
                        'last_update' => isset($score['last_update']) ? Carbon::parse($score['last_update'])->setTimezone('America/Chicago')->format('Y-m-d H:i:s') : null,
                    ]
                );
            }
        }
    }
}
