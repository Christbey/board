<?php

namespace App\Services\CollegeFootball;

use App\Models\CollegeFootballConference;
use App\Models\CollegeFootballGame;
use App\Models\CollegeFootballTeam;
use Illuminate\Support\Facades\Http;

class CollegeFootballApiService
{
    public function findTeam($school)
    {
        return CollegeFootballTeam::where('school', $school)->first();
    }

    public function findOrCreateTeam($school)
    {
        return CollegeFootballTeam::firstOrCreate(['school' => $school]);
    }

    public function findConference($conferenceName)
    {
        return CollegeFootballConference::where('name', $conferenceName)->first();
    }

    public function findOrCreateConference($conferenceName)
    {
        if (is_null($conferenceName)) {
            // Return null if the conference name is null
            return null;
        }

        return CollegeFootballConference::firstOrCreate(['name' => $conferenceName]);
    }

    public function findCollegeGame($gameId)
    {
        return CollegeFootballGame::where('id', $gameId)->first();
    }

    public function fetchFromApi($url)
    {
        return Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('collegefootball.api_key'),
        ])->get($url);
    }
}
