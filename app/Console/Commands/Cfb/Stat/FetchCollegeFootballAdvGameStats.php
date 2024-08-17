<?php
// KEEP THIS FILE IT WORKS
namespace App\Console\Commands\Cfb\Stat;

use App\Models\CollegeFootballAdvGameStat;
use App\Models\CollegeFootballTeam;
use App\Traits\CollegeFootball\CollegeFootballAdvGameTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchCollegeFootballAdvGameStats extends Command
{
    use CollegeFootballAdvGameTrait;

    protected $signature = 'fetch:college-football-adv-game-stats {year?} {week?}';
    protected $description = 'Fetch and store college football advanced game stats from API';

    public function handle()
    {
        $year = $this->argument('year') ?? config('collegefootball.default_year');
        $week = $this->argument('week');
        $url = $this->buildApiUrl($year, $week);

        $response = $this->fetchStatsFromApi($url);

        if ($response->successful()) {
            $this->processStats($response->json(), $year);
        } else {
            $this->error('Failed to fetch data from API');
        }
    }

    private function buildApiUrl($year, $week)
    {
        $url = config('collegefootball.api_base_url') . "/stats/game/advanced?year=$year";

        if ($week) {
            $url .= "&week=$week";
        }

        return $url;
    }

    private function fetchStatsFromApi($url)
    {
        return Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('collegefootball.api_key'),
        ])->get($url);
    }

    private function processStats(array $stats, $year)
    {
        foreach ($stats as $stat) {
            $this->storeStat($stat, $year);
            $this->storeStat($this->reverseStat($stat), $year);
        }
    }

    private function storeStat(array $stat, $year)
    {
        $team = $this->findOrCreateTeam($stat['team']);
        $opponent = $this->findOrCreateTeam($stat['opponent']);

        CollegeFootballAdvGameStat::updateOrCreate(
            [
                'game_id' => $stat['gameId'],
                'team_id' => $team->id,
            ],
            array_merge(
                [
                    'season' => $year,
                    'week' => $stat['week'],
                    'opponent_id' => $opponent->id,
                ],
                $this->extractOffenseStats($stat['offense']),
                $this->extractDefenseStats($stat['defense'])
            )
        );
    }

    private function findOrCreateTeam($school)
    {
        return CollegeFootballTeam::firstOrCreate(['school' => $school]);
    }
}
