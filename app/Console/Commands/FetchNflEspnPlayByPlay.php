<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\NflEspnPlayByPlay;
use Carbon\Carbon;
use Log;

class FetchNflEspnPlayByPlay extends Command
{
    protected $signature = 'fetch:nfl-espn-play-by-play';
    protected $description = 'Fetch and store NFL play-by-play data from ESPN API';

    public function handle()
    {
        $url = 'https://sports.core.api.espn.com/v2/sports/football/leagues/nfl/events/401249063/competitions/401249063/plays?limit=300';

        $response = Http::get($url);

        if ($response->successful()) {
            $plays = $response->json('items');

            foreach ($plays as $play) {
                // Debugging: Log the play data to inspect its structure
                Log::info('Play data:', $play);

                $playData = [
                    'game_id' => '401249063', // Example game ID, this would be dynamic in real scenario
                    'sequenceNumber' => $play['sequenceNumber'],
                    'type_id' => $play['type']['id'],
                    'type_text' => $play['type']['text'],
                    'type_abbreviation' => $play['type']['abbreviation'] ?? null,
                    'text' => $play['text'],
                    'shortText' => $play['shortText'] ?? null,
                    'alternativeText' => $play['alternativeText'] ?? null,
                    'shortAlternativeText' => $play['shortAlternativeText'] ?? null,
                    'awayScore' => $play['awayScore'] ?? null,
                    'homeScore' => $play['homeScore'] ?? null,
                    'period_number' => $play['period']['number'] ?? null,
                    'clock_value' => $play['clock']['value'] ?? null,
                    'clock_displayValue' => $play['clock']['displayValue'] ?? null,
                    'scoringPlay' => $play['scoringPlay'],
                    'scoreValue' => $play['scoreValue'] ?? null,
                    'modified' => Carbon::parse($play['modified'] ?? null),
                    'team_id' => isset($play['team']) ? $this->extractIdFromUrl($play['team']['$ref']) : null,
                    'wallclock' => Carbon::parse($play['wallclock'] ?? null),
                    'drive_id' => isset($play['drive']) ? (int)$this->extractIdFromUrl($play['drive']['$ref']) : null,
                    'start_down' => $play['start']['down'] ?? null,
                    'start_distance' => $play['start']['distance'] ?? null,
                    'start_yardLine' => $play['start']['yardLine'] ?? null,
                    'start_yardsToEndzone' => $play['start']['yardsToEndzone'] ?? null,
                    'start_downDistanceText' => $play['start']['downDistanceText'] ?? null,
                    'start_shortDownDistanceText' => $play['start']['shortDownDistanceText'] ?? null,
                    'start_possessionText' => $play['start']['possessionText'] ?? null,
                    'start_team_id' => isset($play['start']['team']) ? $this->extractIdFromUrl($play['start']['team']['$ref']) : null,
                    'end_down' => $play['end']['down'] ?? null,
                    'end_distance' => $play['end']['distance'] ?? null,
                    'end_yardLine' => $play['end']['yardLine'] ?? null,
                    'end_yardsToEndzone' => $play['end']['yardsToEndzone'] ?? null,
                    'end_downDistanceText' => $play['end']['downDistanceText'] ?? null,
                    'end_shortDownDistanceText' => $play['end']['shortDownDistanceText'] ?? null,
                    'end_possessionText' => $play['end']['possessionText'] ?? null,
                    'end_team_id' => isset($play['end']['team']) ? $this->extractIdFromUrl($play['end']['team']['$ref']) : null,
                    'statYardage' => $play['statYardage'] ?? null,
                ];

                // Debugging: Log the play data array to inspect values before saving
                Log::info('Play data array:', $playData);

                $playModel = NflEspnPlayByPlay::updateOrCreate(
                    ['sequenceNumber' => $play['sequenceNumber']],
                    $playData
                );

                // Handle participants
                if (isset($play['participants'])) {
                    foreach ($play['participants'] as $participant) {
                        $athleteId = $this->extractIdFromUrl($participant['athlete']['$ref']);
                        $position = isset($participant['position']) ? $this->extractIdFromUrl($participant['position']['$ref']) : null;
                        $participantType = $participant['type'] ?? null;
                        $participantOrder = $participant['order'] ?? null;

                        $playModel->athlete_id = $athleteId;
                        $playModel->position = $position;
                        $playModel->participant_type = $participantType;
                        $playModel->participant_order = $participantOrder;
                        $playModel->save();
                    }
                }
            }

            $this->info('NFL play-by-play data fetched and stored successfully.');
        } else {
            $this->error('Failed to fetch data from ESPN API.');
        }
    }

    private function extractIdFromUrl($url)
    {
        $parts = explode('?', rtrim($url, '/'));
        $pathParts = explode('/', $parts[0]);
        return end($pathParts);
    }
}
