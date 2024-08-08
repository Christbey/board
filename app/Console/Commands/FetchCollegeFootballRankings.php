<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CollegeFootballRanking;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballRankings extends Command
{
    protected $signature = 'fetch:college-football-rankings {year=2023} {week=1} {seasonType=regular}';
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
            'Authorization' => 'Bearer 4b/N6meGdvO3k52FMU375HldXVcg+iNk6o/SMYATiNL3LUkg0LNRcvUKg97pbGrT',
        ])->get("https://api.collegefootballdata.com/rankings?year={$year}&week={$week}&seasonType={$seasonType}");

        if ($response->successful()) {
            $rankings = $response->json();

            foreach ($rankings as $rankingWeek) {
                foreach ($rankingWeek['polls'] as $poll) {
                    foreach ($poll['ranks'] as $rank) {
                        CollegeFootballRanking::updateOrCreate(
                            [
                                'season' => $rankingWeek['season'],
                                'season_type' => $rankingWeek['seasonType'],
                                'week' => $rankingWeek['week'],
                                'poll' => $poll['poll'],
                                'rank' => $rank['rank'],
                                'school' => $rank['school']
                            ],
                            [
                                'conference' => $rank['conference'],
                                'first_place_votes' => $rank['firstPlaceVotes'],
                                'points' => $rank['points']
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
