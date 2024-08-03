<?php

namespace App\Console\Commands\Espn\Season;

use App\Models\NflEspnAthlete;
use App\Models\NflEspnFuture;
use App\Models\NflEspnTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Futures extends Command
{
    protected $signature = 'espn:futures {season}';
    protected $description = 'Fetch NFL futures from ESPN API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $season = $this->argument('season');
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

                $this->info("NFL futures for season {$season} fetched and stored successfully.");
            } else {
                $this->error("No futures data found for season {$season}.");
            }
        } else {
            $this->error("Failed to fetch NFL futures for season {$season}.");
        }
    }
}
