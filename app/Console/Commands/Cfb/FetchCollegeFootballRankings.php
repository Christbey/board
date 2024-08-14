<?php

namespace App\Console\Commands\Cfb;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballRanking;
use App\Models\CollegeFootballTeam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballRankings extends Command
{
    protected $signature = 'fetch:college-football-rankings {year=2024} {week=1} {seasonType=regular}';
    protected $description = 'Fetch college football rankings from the API and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $week = $this->argument('week');
        $seasonType = $this->argument('seasonType');

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('COLLEGE_FOOTBALL_DATA_API_KEY'),
        ])->get("https://api.collegefootballdata.com/rankings?year={$year}&week={$week}&seasonType={$seasonType}");

        if ($response->successful()) {
            $rankings = $response->json();

            foreach ($rankings as $rankingWeek) {
                foreach ($rankingWeek['polls'] as $poll) {
                    foreach ($poll['ranks'] as $rank) {
                        // Find the corresponding team
                        $team = CollegeFootballTeam::where('school', $rank['school'])->first();
                        if (!$team) {
                            $this->error("Team not found for: {$rank['school']}");
                            continue;
                        }

                        // Find the corresponding conference
                        $conference = CollegeFootballConference::where('abbreviation', $rank['conference'])
                            ->orWhere('name', $rank['conference'])
                            ->first();

                        if (!$conference) {
                            $this->error("Conference not found for: {$rank['conference']}");
                            continue;
                        }

                        // Update or create the ranking with team_id and conference_id
                        CollegeFootballRanking::updateOrCreate(
                            [
                                'season' => $rankingWeek['season'],
                                'season_type' => $rankingWeek['seasonType'],
                                'week' => $rankingWeek['week'],
                                'poll' => $poll['poll'],
                                'rank' => $rank['rank'],
                                'team_id' => $team->id,
                                'conference_id' => $conference->id,
                            ],
                            [
                                'first_place_votes' => $rank['firstPlaceVotes'] ?? null,
                                'points' => $rank['points'] ?? null,
                            ]
                        );
                    }
                }
            }

            $this->info("College football rankings for year {$year}, week {$week}, and season type {$seasonType} fetched and saved successfully.");
        } else {
            $this->error('Failed to fetch data from the API.');
        }
    }
}
