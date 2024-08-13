<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CollegeFootballTeam;
use App\Models\CollegeFootballAdvGameStat;
use App\Models\CollegeFootballTalent;
use App\Models\CollegeFootballFpiRating;

class CfbTeamDetails extends Component
{
    public $teamId;
    public $year;
    public $teamData;
    public $talentData;
    public $fpiRating;
    public $advStats;
    public $games;
    public $teamColor;

    public function mount($teamId, $year = null)
    {
        $this->teamId = $teamId;
        $this->year = $year ?? date('Y');

        $this->loadData();
    }

    public function loadData()
    {
        $this->teamData = CollegeFootballTeam::with('conference')->findOrFail($this->teamId);
        $this->teamColor = $this->teamData->color;
        $this->talentData = CollegeFootballTalent::where('team_id', $this->teamId)->where('year', $this->year)->first();
        $this->fpiRating = CollegeFootballFpiRating::where('team_id', $this->teamId)->where('year', $this->year)->first();
        $this->advStats = CollegeFootballAdvGameStat::where('team_id', $this->teamId)->where('season', $this->year)->first();
    }

    public function render()
    {
        return view('livewire.cfb-team-details');
    }
}
