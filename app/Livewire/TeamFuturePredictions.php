<?php

namespace App\Livewire;

use App\Models\NflEspnFuture;
use Livewire\Component;

class TeamFuturePredictions extends Component
{
    public $teamId;
    public $futures;
    public $filterProviderName = '';
    public $allProviderNames;

    public function mount($teamId)
    {
        $this->teamId = $teamId;
        $this->fetchAllProviderNames();
        $this->fetchFutures();
    }

    public function updatedFilterProviderName()
    {
        $this->fetchFutures();
    }

    public function fetchAllProviderNames()
    {
        $this->allProviderNames = NflEspnFuture::where('team_id', $this->teamId)
            ->distinct()
            ->pluck('provider_name');
    }

    public function fetchFutures()
    {
        $query = NflEspnFuture::where('team_id', $this->teamId);

        if ($this->filterProviderName) {
            $query->where('provider_name', $this->filterProviderName);
        }

        $this->futures = $query->get();
    }

    public function render()
    {
        return view('livewire.team-future-predictions');
    }
}
