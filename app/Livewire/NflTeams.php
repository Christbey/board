<?php

// In NflTeams.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\NflTeam;
use App\Models\NflTeamSchedule;
use App\Models\NflOdds;
use Carbon\Carbon;
use App\Services\Elo\EloRatingSystem;

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

        $seasonStartDate = Carbon::parse('2024-09-01');
        $seasonEndDate = Carbon::parse('2024-12-31');

        foreach ($this->teams as $team) {
            $schedules = NflTeamSchedule::with('odds')
                ->where(function ($query) use ($team) {
                    $query->where('team_id_home', $team->id)
                        ->orWhere('team_id_away', $team->id);
                })
                ->whereBetween('game_date', [$seasonStartDate, $seasonEndDate])
                ->whereNull('home_result')
                ->whereNull('away_result')
                ->orderBy('game_date')
                ->take(3)
                ->get();

            // Debugging
            logger()->info('Schedules for team:', ['team' => $team->name, 'schedules' => $schedules]);

            $this->nextOpponents[$team->id] = $schedules;
        }

        // Debugging
        logger()->info('Next opponents:', ['nextOpponents' => $this->nextOpponents]);
    }

    public function openModal($teamId)
    {
        $this->selectedTeam = $this->teams->find($teamId);
        $this->showModal = true;
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
