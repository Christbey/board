<?php

namespace App\Jobs\CollegeFootball;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballRanking;
use App\Models\CollegeFootballTeam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCollegeFootballRankingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $year;
    protected $week;
    protected $seasonType;

    protected $teams = [];
    protected $conferences = [];

    public function __construct($year, $week, $seasonType)
    {
        $this->year = $year;
        $this->week = $week;
        $this->seasonType = $seasonType;
    }

    public function handle()
    {
        // Cache teams and conferences to avoid repetitive queries
        $this->cacheTeamsAndConferences();

        // Fetch rankings from API
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('collegefootball.api_key'),
        ])->get("https://api.collegefootballdata.com/rankings?year={$this->year}&week={$this->week}&seasonType={$this->seasonType}");

        if ($response->successful()) {
            $rankings = $response->json();
            $rankingData = [];

            foreach ($rankings as $rankingWeek) {
                foreach ($rankingWeek['polls'] as $poll) {
                    // Check if rankings already exist for this poll, week, and season type
                    $existingRankings = CollegeFootballRanking::where('season', $rankingWeek['season'])
                        ->where('week', $rankingWeek['week'])
                        ->where('season_type', $rankingWeek['seasonType'])
                        ->where('poll', $poll['poll'])
                        ->exists();

                    if ($existingRankings) {
                        Log::info("No new rankings created. Rankings for year {$this->year}, week {$this->week}, season type {$this->seasonType}, and poll {$poll['poll']} already exist.");
                        continue; // Skip processing this poll
                    }

                    foreach ($poll['ranks'] as $rank) {
                        $teamId = $this->getTeamId($rank['school']);
                        $conferenceId = $this->getConferenceId($rank['conference']);

                        if ($teamId && $conferenceId) {
                            $rankingData[] = [
                                'season' => $rankingWeek['season'],
                                'season_type' => $rankingWeek['seasonType'],
                                'week' => $rankingWeek['week'],
                                'poll' => $poll['poll'],
                                'rank' => $rank['rank'],
                                'team_id' => $teamId,
                                'conference_id' => $conferenceId,
                                'first_place_votes' => $rank['firstPlaceVotes'] ?? null,
                                'points' => $rank['points'] ?? null,
                            ];
                        }
                    }
                }
            }

            if (!empty($rankingData)) {
                // Insert or update rankings in batch
                CollegeFootballRanking::upsert(
                    $rankingData,
                    ['season', 'season_type', 'week', 'poll', 'rank', 'team_id', 'conference_id'],
                    ['first_place_votes', 'points']
                );

                Log::info("College football rankings for year {$this->year}, week {$this->week}, and season type {$this->seasonType} fetched and saved successfully.");
            } else {
                Log::info('No new ranking data to insert or update.');
            }
        } else {
            Log::error('Failed to fetch data from the API.');
        }
    }

    protected function cacheTeamsAndConferences()
    {
        $this->teams = CollegeFootballTeam::pluck('id', 'school')->all();
        $this->conferences = CollegeFootballConference::pluck('id', 'abbreviation')->merge(
            CollegeFootballConference::pluck('id', 'name')
        )->all();
    }

    protected function getTeamId($school)
    {
        if (isset($this->teams[$school])) {
            return $this->teams[$school];
        }

        Log::warning("Team not found for: {$school}");
        return null;
    }

    protected function getConferenceId($conference)
    {
        if (isset($this->conferences[$conference])) {
            return $this->conferences[$conference];
        }

        Log::warning("Conference not found for: {$conference}");
        return null;
    }
}
