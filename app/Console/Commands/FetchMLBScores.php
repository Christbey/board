<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Events\MLBScoresFetched;

class FetchMLBScores extends Command
{
    protected $signature = 'mlb:scores:fetch';
    protected $description = 'Fetch the latest MLB scores from the API';

    public function __construct()
    {
        parent::__construct();
    }

    public static function dispatch()
    {
    }

    public function handle()
    {
        $response = Http::get(env('ODDS_API_BASE_URL') . '/sports/baseball_mlb/scores', [
            'apiKey' => env('ODDS_API_KEY'),
            'daysFrom' => 3,
            'dateFormat' => 'iso'
        ]);

        if ($response->successful()) {
            $scores = $response->json();
            event(new MLBScoresFetched($scores));
            $this->info('MLB scores fetched and event dispatched.');
        } else {
            $this->error('Failed to fetch MLB scores: ' . $response->body());
        }

        return 0;
    }
}
