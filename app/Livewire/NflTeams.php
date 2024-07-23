<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NflTeam;
use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use Carbon\Carbon;
use App\Services\Elo\EloRatingSystem;
use App\Helpers\NflHelper;

class NflTeams extends Component
{
    public $teams;
    public $expectedWins;
    public $nextOpponents = [];
    public $selectedTeam;
    public $showModal = false;

    protected $listeners = ['openModal' => 'openModal'];

    public function mount(EloRatingSystem $eloRatingSystem)
    {
        $this->teams = NflTeam::all();
        $this->expectedWins = $eloRatingSystem->calculateExpectedWinsForTeams();
    }

    public function openModal($teamId)
    {
        $this->selectedTeam = NflTeam::find($teamId);

        if ($this->selectedTeam) {
            [$seasonStartDate, $seasonEndDate] = NflHelper::getSeasonDateRange(2024);

            $schedules = NflTeamSchedule::with('odds')
                ->where(function ($query) use ($teamId) {
                    $query->where('team_id_home', $teamId)
                        ->orWhere('team_id_away', $teamId);
                })
                ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
                ->whereNull('home_result')
                ->whereNull('away_result')
                ->orderBy('game_date')
                ->take(3)
                ->get();

            $this->nextOpponents[$teamId] = $schedules;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.nfl-teams');
    }
}
