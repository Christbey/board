<?php

// app/Jobs/FetchNflFuturesJob.php

namespace App\Jobs\Nfl;

use App\Models\NflEspnAthlete;
use App\Models\NflEspnFuture;
use App\Models\NflEspnTeam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchNflFuturesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $season;

    /**
     * Create a new job instance.
     *
     * @param int $season
     */
    public function __construct(int $season)
    {
        $this->season = $season;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $season = $this->season;
        $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/seasons/{$season}/futures";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['items']) && count($data['items']) > 0) {
                foreach ($data['items'] as $item) {
                    $futureId = $item['id'];
                    $name = $item['name'];
                    $displayName = $item['displayName'];

                    foreach ($item['futures'] as $future) {
                        $provider = $future['provider'] ?? [];
                        $providerId = $provider['id'] ?? null;
                        $providerName = $provider['name'] ?? null;

                        foreach ($future['books'] as $book) {
                            $athleteId = null;
                            $teamId = null;
                            if (isset($book['athlete']['$ref'])) {
                                $athleteUrlParts = explode('/', rtrim($book['athlete']['$ref'], '/'));
                                $athleteId = (int)end($athleteUrlParts);
                                if (!NflEspnAthlete::where('athlete_id', $athleteId)->exists()) {
                                    $athleteId = null; // Set to null if athlete does not exist
                                }
                            }
                            if (isset($book['team']['$ref'])) {
                                $teamUrlParts = explode('/', rtrim($book['team']['$ref'], '/'));
                                $teamId = (int)end($teamUrlParts);
                                if (!NflEspnTeam::where('team_id', $teamId)->exists()) {
                                    $teamId = null; // Set to null if team does not exist
                                }
                            }
                            $value = $book['value'] ?? null;

                            NflEspnFuture::updateOrCreate(
                                [
                                    'future_id' => $futureId,
                                    'provider_id' => $providerId,
                                    'athlete_id' => $athleteId,
                                    'team_id' => $teamId,
                                ],
                                [
                                    'name' => $name,
                                    'display_name' => $displayName,
                                    'provider_name' => $providerName,
                                    'value' => $value,
                                ]
                            );
                        }
                    }
                }

                Log::info("NFL futures for season {$season} fetched and stored successfully.");
            } else {
                Log::warning("No futures data found for season {$season}.");
            }
        } else {
            Log::error("Failed to fetch NFL futures for season {$season}.");
        }
    }
}
