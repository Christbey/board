<?php

namespace App\Jobs;

use App\Events\NbaScoresFetched;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchNbaScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Fetch NBA scores
        $response = Http::get('https://api.the-odds-api.com/v4/sports/basketball_nba/scores', [
            'apiKey' => '9f1d9176fa7c6c47ea169f2ff007c8fa',
            'daysFrom' => 3,
            'dateFormat' => 'iso'
        ]);

        if ($response->successful()) {
            $scores = $response->json();

            // Dispatch event with the fetched scores
            event(new NbaScoresFetched($scores));
        } else {
            // Handle unsuccessful response
            Log::error('Failed to fetch NBA scores: ' . $response->status());
        }
    }
}
