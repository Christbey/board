<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NflEspnTeamStat;

class EspnTeamStats extends Component
{
    public $teamId;
    public $season;
    public $category;
    public $seasons = [];
    public $categories = [];
    public $statistics;

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->seasons = NflEspnTeamStat::where('team_id', $this->teamId)
            ->distinct()
            ->pluck('season');
        $this->categories = NflEspnTeamStat::where('team_id', $this->teamId)
            ->distinct()
            ->pluck('category');
    }

    public function fetchStatistics()
    {
        $query = NflEspnTeamStat::where('team_id', $this->teamId);

        if ($this->season) {
            $query->where('season', $this->season);
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $this->statistics = $query->get();
    }

    public function render()
    {
        return view('livewire.espn-team-stats');
    }
}
