<?php

namespace App\Console\Commands\Espn\Events;

use App\Models\NflEspnEventOdd;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EventOdds extends Command
{
    protected $signature = 'espn:nfl-odds {event_id}';
    protected $description = 'Fetch NFL event odds from ESPN API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $eventId = $this->argument('event_id');
        $competitionId = $eventId; // Since event_id and competition_id are the same

        $url = "https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/events/{$eventId}/competitions/{$competitionId}/odds";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['items']) && count($data['items']) > 0) {
                foreach ($data['items'] as $item) {
                    $provider = $item['provider'] ?? [];
                    $awayTeamOdds = $item['awayTeamOdds'] ?? [];
                    $homeTeamOdds = $item['homeTeamOdds'] ?? [];
                    $links = $item['links'] ?? [];
                    $openOdds = $item['open'] ?? [];
                    $currentOdds = $item['current'] ?? [];

                    NflEspnEventOdd::updateOrCreate(
                        [
                            'event_id' => $eventId,
                            'competition_id' => $competitionId,
                            'provider_id' => $provider['id'] ?? null,
                        ],
                        [
                            'provider_name' => $provider['name'] ?? null,
                            'details' => $item['details'] ?? null,
                            'over_under' => $item['overUnder'] ?? null,
                            'spread' => $item['spread'] ?? null,
                            'over_odds' => $item['overOdds'] ?? null,
                            'under_odds' => $item['underOdds'] ?? null,
                            'away_team_odds' => json_encode($awayTeamOdds),
                            'home_team_odds' => json_encode($homeTeamOdds),
                            'links' => json_encode($links),
                            'open_odds' => json_encode($openOdds),
                            'current_odds' => json_encode($currentOdds),
                        ]
                    );
                }

                $this->info("NFL event odds for event {$eventId} fetched and stored successfully.");
            } else {
                $this->error("No odds data found for event {$eventId}.");
            }
        } else {
            $this->error("Failed to fetch NFL event odds for event {$eventId}.");
        }
    }
}
