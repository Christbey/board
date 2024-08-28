<?php

namespace App\Console\Commands\Cfb\Stat;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballSpConferenceRating;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballSpConferenceRatings extends Command
{
    protected $signature = 'fetch:college-football-sp-conference-ratings {year=2024}';
    protected $description = 'Fetch college football SP+ conference ratings from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('collegefootball.api_key'),
        ])->get("https://api.collegefootballdata.com/ratings/sp/conferences?year={$year}");

        if ($response->successful()) {
            $ratings = $response->json();

            foreach ($ratings as $rating) {
                $conference = CollegeFootballConference::where('abbreviation', $rating['conference'])->orWhere('name', $rating['conference'])->first();

                if (!$conference) {
                    $this->error("Conference not found for: {$rating['conference']}");
                    continue;
                }

                CollegeFootballSpConferenceRating::updateOrCreate(
                    [
                        'year' => $year,
                        'conference_id' => $conference->id,
                    ],
                    [
                        'rating' => $rating['rating'] ?? null,
                        'ranking' => $rating['ranking'] ?? null,
                        'offense_rating' => $rating['offenseRating'] ?? null,
                        'defense_rating' => $rating['defenseRating'] ?? null,
                        'special_teams_rating' => $rating['specialTeamsRating'] ?? null,
                    ]
                );
            }

            $this->info("College football SP+ conference ratings for year {$year} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
