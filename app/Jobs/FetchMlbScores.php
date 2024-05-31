<?php


namespace App\Jobs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchMlbScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Fetch MLB scores
        $response = Http::get('https://api.the-odds-api.com/v4/sports/baseball_mlb/scores', [
            'apiKey' => '9f1d9176fa7c6c47ea169f2ff007c8fa',
            'daysFrom' => 3,
            'dateFormat' => 'iso'
        ]);

        if ($response->successful()) {
            $scores = $response->json();

            // Process the scores as needed

            // Dispatch event or perform any other actions

            // Update the view with the fetched scores
            event(new MlbScoresFetched($scores));
        } else {
            // Handle unsuccessful response
            Log::error('Failed to fetch MLB scores: ' . $response->status());
        }
    }
}
