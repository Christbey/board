<?php

namespace App\Console\Commands\Cfb\Team;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballTeams extends Command
{
    protected $signature = 'fetch:college-football-teams';
    protected $description = 'Fetch college football teams from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY'),
        ])->get('https://api.collegefootballdata.com/teams');

        if ($response->successful()) {
            $teams = $response->json();

            foreach ($teams as $team) {
                // Skip teams with invalid or missing data
                if (empty($team['id']) || empty($team['school'])) {
                    $this->error('Skipping team due to missing id or school name.');
                    continue;
                }

                // Ensure the conference exists
                $conferenceAbbreviation = $team['conference'] ?? null;
                if (!$conferenceAbbreviation) {
                    $this->error('Skipping team due to missing conference: ' . $team['school']);
                    continue;
                }

                // Find or create the conference by abbreviation
                $conference = CollegeFootballConference::updateOrCreate(
                    ['abbreviation' => $conferenceAbbreviation],
                    [
                        'name' => $team['conference'] ?? $conferenceAbbreviation, // Use the provided name or fallback to abbreviation
                        'short_name' => $team['conference_short_name'] ?? null, // Assuming `conference_short_name` exists in the API response
                        'classification' => $team['classification'] ?? null // Assuming `classification` exists in the API response
                    ]
                );

                if (!$conference) {
                    $this->error('Conference not found or created for abbreviation/name: ' . $team['conference']);
                    continue; // Skip this team if the conference cannot be created or found
                }

                $location = $team['location'] ?? [];

                // Update or create the team with the conference_id
                CollegeFootballTeam::updateOrCreate(
                    ['id' => $team['id']],
                    [
                        'school' => $team['school'],
                        'mascot' => $team['mascot'],
                        'abbreviation' => $team['abbreviation'] ?? null,
                        'alt_name1' => $team['alt_name1'] ?? null,
                        'alt_name2' => $team['alt_name2'] ?? null,
                        'alt_name3' => $team['alt_name3'] ?? null,
                        'color' => $team['color'] ?? null,
                        'alt_color' => $team['alt_color'] ?? null,
                        'logos' => json_encode($team['logos'] ?? []),
                        'twitter' => $team['twitter'] ?? null,
                        'conference_id' => $conference->id, // Link to conference
                        'venue_id' => $location['venue_id'] ?? null,
                        'venue_name' => $location['name'] ?? null,
                        'city' => $location['city'] ?? null,
                        'state' => $location['state'] ?? null,
                        'zip' => $location['zip'] ?? null,
                        'country_code' => $location['country_code'] ?? null,
                        'timezone' => $location['timezone'] ?? null,
                        'latitude' => $location['latitude'] ?? null,
                        'longitude' => $location['longitude'] ?? null,
                        'elevation' => $location['elevation'] ?? null,
                        'capacity' => $location['capacity'] ?? null,
                        'year_constructed' => $location['year_constructed'] ?? null,
                        'grass' => $location['grass'] ?? null,
                        'dome' => $location['dome'] ?? null,
                    ]
                );
            }

            $this->info('College football teams fetched and saved successfully.');
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
