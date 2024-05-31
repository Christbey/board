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
        $url = 'https://api.the-odds-api.com/v4/sports/basketball_nba/scores';
        $params = [
            'apiKey' => '9f1d9176fa7c6c47ea169f2ff007c8fa',
            'daysFrom' => 3,
            'dateFormat' => 'iso'
        ];

        $response = Http::get($url, $params);

        if ($response->successful()) {
            $scores = $response->json();
            event(new NbaScoresFetched($scores));
        } else {
            Log::error('Failed to fetch NBA scores', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        }
    }
}
